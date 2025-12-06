<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Entity\User;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ChangeAssigneeAction extends AbstractController {

    public const string ASSIGNEE_CSRF_TOKEN_ID = 'problem_assignee';

    #[Route(path: '/problems/{uuid}/assignee', name: 'change_assignee', methods: ['POST'])]
    #[NotFoundRedirect(redirectRoute: 'problems', flashMessage: 'problems.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'problems', flashMessage: 'problems.not_found')]
    public function __invoke(
        #[CurrentUser] User $user,
        Request $request,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse {
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

        $problemRepository->persist($problem);

        $this->addFlash('success', 'problems.assignee.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }
}