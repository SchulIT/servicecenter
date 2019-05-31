<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemType as ProblemTypeEntity;
use App\Form\ProblemType;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProblemsController extends AbstractController {
    private $repository;

    public function __construct(ProblemRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/problems/add", name="new_problem")
     */
    public function add(Request $request, EventDispatcherInterface $eventDispatcher) {
        $problem = new Problem();

        $form = $this->createForm(ProblemType::class, $problem);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($problem);

            $this->addFlash('success', 'problems.add.success');
            return $this->redirectToRoute('show_problem', [
                'id' => $problem->getId()
            ]);
        }

        return $this->render('problems/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/problems/{id}/edit", name="edit_problem")
     */
    public function edit(Request $request, Problem $problem) {
        $this->denyAccessUnlessGranted(ProblemVoter::EDIT, $problem);

        $form = $this->createForm(ProblemType::class, $problem, [ 'show_options' => $this->isGranted('ROLE_ADMIN') ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($problem);

            $this->addFlash('success', 'problems.edit.success');
            return $this->redirectToRoute('show_problem', [ 'id' => $problem->getId() ]);
        }

        return $this->render('problems/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/problems/{id}", name="show_problem")
     */
    public function show(Request $request, Problem $problem) {
        $this->denyAccessUnlessGranted(ProblemVoter::VIEW, $problem);

        if($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_show_problem', [
                'id' => $problem->getId()
            ]);
        }

        return $this->render('problems/show.html.twig', [
            'problem' => $problem
        ]);
    }

    /**
     * @Route("/problems/add/ajax", name="problem_ajax")
     */
    public function ajax(Request $request, DeviceRepositoryInterface $deviceRepository) {
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
        foreach($types as $type) {
            $result[] = [
                'id' => $type->getId(),
                'name' => $type->getName()
            ];
        }

        return new JsonResponse($result);
    }
}