<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemType as ProblemTypeEntity;
use App\Entity\Room;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\Models\ProblemDto;
use App\Form\ProblemDtoType;
use App\Form\ProblemType;
use App\Helper\Problems\BulkActionManager;
use App\Helper\Problems\History\HistoryResolver;
use App\Repository\CommentRepositoryInterface;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\PaginationQuery;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\ProblemTypeRepository;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Security\Voter\CommentVoter;
use App\Security\Voter\ProblemVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemsController extends AbstractController {
    public const string ASSIGNEE_CSRF_TOKEN_ID = 'problem_assignee';
    public const string BULK_CSRF_TOKEN_ID = 'problem_bulk';
    public const string STATUS_CSRF_TOKEN_ID = 'problem_status';
    public const string MAINTENANCE_CSRF_TOKEN_ID = 'problem_maintenance';

    public function __construct(private readonly ProblemRepositoryInterface $problemRepository, private readonly CommentRepositoryInterface $commentRepository)
    {
    }

    #[Route(path: '/problems/add', name: 'new_problem')]
    public function add(Request $request): RedirectResponse|Response {
        $problemDto = new ProblemDto();

        $form = $this->createForm(ProblemDtoType::class, $problemDto);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $lastUuid = null;
            $problems = 0;

            foreach($problemDto->getDevices() as $device) {
                $problem = (new Problem())
                    ->setContent($problemDto->getContent())
                    ->setProblemType($problemDto->getProblemType())
                    ->setPriority($problemDto->getPriority())
                    ->setDevice($device);

                $this->problemRepository->persist($problem);
                ++$problems;
                $lastUuid = $problem->getUuid();
            }

            $this->addFlash('success', 'problems.add.success');

            if($problems === 1) {
                return $this->redirectToRoute('show_problem', [
                    'uuid' => $lastUuid
                ]);

            }

            return $this->redirectToRoute('problems');
        }

        return $this->render('problems/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/problems/{uuid}/edit', name: 'edit_problem')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(ProblemVoter::EDIT, $problem);

        $form = $this->createForm(ProblemType::class, $problem);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->problemRepository->persist($problem);

            $this->addFlash('success', 'problems.edit.success');
            return $this->redirectToRoute('show_problem', [ 'uuid' => $problem->getUuid() ]);
        }

        return $this->render('problems/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/problems/xhr/devices', name: 'devices_ajax')]
    public function getDevicesXhr(Request $request, RoomRepositoryInterface $roomRepository, TranslatorInterface $translator): JsonResponse {
        $roomId = $request->query->get('room', null);

        if($roomId === null) {
            return new JsonResponse([]);
        }

        $room = $roomRepository->findOneById(intval($roomId));

        if(!$room instanceof Room) {
            throw new NotFoundHttpException();
        }

        $devices = $room->getDevices();

        $result = [ ];

        $result[] = [
            'value' => '',
            'placeholder' => true,
            'label' => $translator->trans('label.choose.device')
        ];

        /** @var Device $device */
        foreach($devices as $device) {
            $result[] = [
                'value' => $device->getId(),
                'label' => sprintf('%s (%s)', $device->getName(), $device->getType()->getName()),
                'customProperties' => [
                    'type' => $device->getType()->getId()
                ]
            ];
        }

        return new JsonResponse($result);
    }

    #[Route(path: '/problems/existing', name: 'existing_problems_ajax')]
    public function xhrExistingProblems(Request $request): Response {
        $typeId = null;

        if($request->query->has('type')) {
            $typeId = $request->query->getInt('type');
        }

        $deviceIds = $request->query->get('devices');

        if(!is_array($deviceIds) || count($deviceIds) === 0) {
            return $this->render('problems/existing.html.twig', [
                'problems' => null
            ]);
        }

        $problems = $this->problemRepository->findOpenByDeviceIds($deviceIds, $typeId);

        return $this->render('problems/existing.html.twig', [
            'problems' => $problems
        ]);
    }

    #[Route(path: '/problems/add/ajax', name: 'problem_ajax')]
    public function ajax(Request $request, DeviceRepositoryInterface $deviceRepository, TranslatorInterface $translator): JsonResponse {
        $deviceId = $request->query->get('device', null);

        if($deviceId === null) {
            return new JsonResponse([ ]);
        }

        /** @var Device $device */
        $device = $deviceRepository
            ->findOneById($deviceId);

        if($device === null) {
            throw new NotFoundHttpException();
        }

        /** @var ProblemTypeEntity[] $types */
        $types = $device->getType()->getProblemTypes();

        $result = [ ];

        $result[] = [
            'value' => '',
            'placeholder' => true,
            'label' => $translator->trans('label.choose.problemtype')
        ];

        foreach($types as $type) {
            $result[] = [
                'value' => $type->getId(),
                'label' => $type->getName()
            ];
        }

        return new JsonResponse($result);
    }

    #[Route(path: '/problems', name: 'problems')]
    public function index(
        RoomRepositoryInterface $roomRepository,
        RoomCategoryRepositoryInterface $roomCategoryRepository,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'room')] string|null $roomUuid = null,
        #[MapQueryParameter] string|null $query = null,
        #[MapQueryParameter(name: 'closed')] bool|null $includeClosed = false
    ): RedirectResponse|Response {
        $room = null;

        if($roomUuid !== null) {
            $room = $roomRepository->findOneByUuid($roomUuid);
        }

        return $this->render('problems/index.html.twig', [
            'problems' => $this->problemRepository->findAllPaginated(new PaginationQuery(page: $page), room: $room, query: $query, onlyOpen: !$includeClosed),
            'categories' => $roomCategoryRepository->findAll(),
            'room' => $room,
            'query' => $query,
            'includeClosed' => $includeClosed,
            'csrfTokenBulkId' => self::BULK_CSRF_TOKEN_ID
        ]);
    }

    #[Route(path: '/problems/bulk', name: 'admin_problems_bulk', methods: ['POST'])]
    public function bulk(Request $request, BulkActionManager $bulkActionManager, TranslatorInterface $translator): RedirectResponse {
        if(!$this->isCsrfTokenValid(self::BULK_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.bulk.csrf');

            return $this->redirectToRoute('problems');
        }

        $action = $request->request->get('action');

        if(!$bulkActionManager->canRunAction($action)) {
            throw new BadRequestHttpException();
        }

        $ids = explode(',', $request->request->get('uuids'));

        if(count($ids) == 0) {
            return $this->redirectToRoute('problems');
        }

        $problems = $this->problemRepository
            ->findByUuids($ids);

        $bulkActionManager->run($problems, $action);

        $this->addFlash('success', $translator->trans('problems.bulk.success', ['%count%' => count($problems) ]));
        return $this->redirectToRoute('problems');
    }

    #[Route(path: '/problems/{uuid}', name: 'show_problem')]
    public function show(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem, HistoryResolver $historyResolver): RedirectResponse|Response {
        $comment = (new Comment())
            ->setProblem($problem);

        $formComment = $this->createForm(CommentType::class, $comment, [ ]);
        $formComment->handleRequest($request);

        if($this->isGranted(CommentVoter::ADD, $problem) && $formComment->isSubmitted() && $formComment->isValid()) {
            $this->commentRepository->persist($comment);

            $this->addFlash('success', 'problems.comments.add.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $relatedProblems = $this->problemRepository->findRelatedPaginated(new PaginationQuery(page: 1), $problem);

        return $this->render('problems/show.html.twig', [
            'problem' => $problem,
            'formComment' => $formComment->createView(),
            'assigneeCsrfTokenId' => self::ASSIGNEE_CSRF_TOKEN_ID,
            'statusCsrfTokenId' => self::STATUS_CSRF_TOKEN_ID,
            'maintenanceCsrfTokenId' => self::MAINTENANCE_CSRF_TOKEN_ID,
            'history' => $historyResolver->resolveHistory($problem),
            'participants' => $historyResolver->resolveParticipants($problem),
            'relatedProblemsCount' => $relatedProblems->totalCount
        ]);
    }

    #[Route('/problems/{uuid}/related', name: 'related_problems')]
    public function relatedProblems(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        #[MapQueryParameter] int $page = 1
    ) {
        $relatedProblems = $this->problemRepository->findRelatedPaginated(new PaginationQuery(page: $page), $problem);

        return $this->render('problems/related.html.twig', [
            'problem' => $problem,
            'relatedProblems' => $relatedProblems
        ]);
    }

    #[Route(path: '/problems/{uuid}/maintenance', name: 'change_maintenance', methods: ['POST'])]
    public function toggleMaintenance(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem): RedirectResponse {
        $this->denyAccessUnlessGranted(ProblemVoter::MAINTENANCE, $problem);

        if(!$this->isCsrfTokenValid(self::MAINTENANCE_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.maintenance.csrf');

            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $problem->setIsMaintenance(!$problem->isMaintenance());
        $this->problemRepository->persist($problem);

        $this->addFlash('success', 'problems.maintenance.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }

    #[Route(path: '/problems/{uuid}/status', name: 'change_status', methods: ['POST'])]
    public function toggleStatus(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem): RedirectResponse {
        $this->denyAccessUnlessGranted(ProblemVoter::EDIT, $problem);

        if(!$this->isCsrfTokenValid(self::STATUS_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.status.csrf');

            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $problem->setIsOpen(!$problem->isOpen());
        $this->problemRepository->persist($problem);

        $this->addFlash('success', 'problems.status.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }

    #[Route(path: '/problems/{uuid}/assignee', name: 'change_assignee', methods: ['POST'])]
    public function changeAssignee(#[CurrentUser] ?User $user, Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem): RedirectResponse {
        $this->denyAccessUnlessGranted(ProblemVoter::ASSIGNEE, $problem);

        if(!$this->isCsrfTokenValid(self::ASSIGNEE_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.assignee.csrf');

            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        if(!$problem->getAssignee() instanceof User) {
            $problem->setAssignee($user);
        } else {
            $problem->setAssignee(null);
        }

        $this->problemRepository->persist($problem);

        $this->addFlash('success', 'problems.assignee.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }

    #[Route(path: '/problems/{uuid}/comments/{commentUuid}/edit', name: 'edit_comment')]
    public function editComment(Request $request, #[MapEntity(mapping: ['commentUuid' => 'uuid'])] Comment $comment): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);

        $form = $this->createForm(CommentType::class, $comment, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->persist($comment);

            $this->addFlash('success', 'problems.comments.edit.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $comment->getProblem()->getUuid()
            ]);
        }

        return $this->render('problems/comments/edit.html.twig', [
            'form' => $form->createView(),
            'problem' => $comment->getProblem()
        ]);
    }

    #[Route(path: '/problems/{uuid}/comments/{commentUuid}/remove', name: 'remove_comment')]
    public function removeComment(Request $request, #[MapEntity(mapping: ['commentUuid' => 'uuid'])] Comment $comment): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(CommentVoter::REMOVE, $comment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problems.comments.remove.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->remove($comment);

            $this->addFlash('success', 'problems.comments.remove.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $comment->getProblem()->getUuid()
            ]);
        }

        return $this->render('problems/comments/remove.html.twig', [
            'form' => $form->createView(),
            'problem' => $comment->getProblem()
        ]);
    }

    #[Route(path: '/problems/{uuid}/remove', name: 'remove_problem')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(ProblemVoter::REMOVE, $problem);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problems.remove.confirm',
            'message_parameters' => [
                '%problem%' => sprintf("%s [%s]: %s", $problem->getDevice()->getRoom(), $problem->getDevice(), $problem->getProblemType())
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->problemRepository->remove($problem);
            $this->addFlash('success', 'problems.remove.success');

            return $this->redirectToRoute('problems');
        }

        return $this->render('problems/remove.html.twig', [
            'form' => $form->createView(),
            'problem' => $problem
        ]);
    }
}
