<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChangeStatusAction extends AbstractController {

    public const string STATUS_CSRF_TOKEN_ID = 'problem_status';

    #[Route(path: '/problems/{uuid}/status', name: 'change_status', methods: ['POST'])]
    #[NotFoundRedirect(redirectRoute: 'problems', flashMessage: 'problems.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'problems', flashMessage: 'problems.not_found')]
    public function __invoke(
        Request $request,
        ProblemRepositoryInterface $problemRepository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem
    ): RedirectResponse {
        $this->denyAccessUnlessGranted(ProblemVoter::EDIT, $problem);

        if(!$this->isCsrfTokenValid(self::STATUS_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.status.csrf');

            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $problem->setIsOpen(!$problem->isOpen());
        $problemRepository->persist($problem);

        $this->addFlash('success', 'problems.status.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }
}