<?php

namespace App\Controller\Problem;

use App\Entity\Comment;
use App\Entity\Problem;
use App\Form\CommentType;
use App\Helper\Problems\History\HistoryResolver;
use App\Repository\CommentRepositoryInterface;
use App\Repository\PaginationQuery;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\CommentVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowAction extends AbstractController {
    #[Route(path: '/problems/{uuid}', name: 'show_problem', priority: -100)]
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        HistoryResolver $historyResolver,
        CommentRepositoryInterface $commentRepository,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse|Response {
        $comment = (new Comment())
            ->setProblem($problem);

        $formComment = $this->createForm(CommentType::class, $comment, [ ]);
        $formComment->handleRequest($request);

        if($this->isGranted(CommentVoter::ADD, $problem) && $formComment->isSubmitted() && $formComment->isValid()) {
            $commentRepository->persist($comment);

            $this->addFlash('success', 'problems.comments.add.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $relatedProblems = $problemRepository->findRelatedPaginated(new PaginationQuery(page: 1), $problem);

        return $this->render('problems/show.html.twig', [
            'problem' => $problem,
            'formComment' => $formComment->createView(),
            'assigneeCsrfTokenId' => ChangeAssigneeAction::ASSIGNEE_CSRF_TOKEN_ID,
            'statusCsrfTokenId' => ChangeStatusAction::STATUS_CSRF_TOKEN_ID,
            'maintenanceCsrfTokenId' => ToggleMaintenanceAction::MAINTENANCE_CSRF_TOKEN_ID,
            'history' => $historyResolver->resolveHistory($problem),
            'participants' => $historyResolver->resolveParticipants($problem),
            'relatedProblemsCount' => $relatedProblems->totalCount
        ]);
    }
}