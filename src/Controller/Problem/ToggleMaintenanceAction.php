<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ToggleMaintenanceAction extends AbstractController {

    public const string MAINTENANCE_CSRF_TOKEN_ID = 'problem_maintenance';

    #[Route(path: '/problems/{uuid}/maintenance', name: 'change_maintenance', methods: ['POST'])]
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse {
        $this->denyAccessUnlessGranted(ProblemVoter::MAINTENANCE, $problem);

        if(!$this->isCsrfTokenValid(self::MAINTENANCE_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.maintenance.csrf');

            return $this->redirectToRoute('show_problem', [
                'uuid' => $problem->getUuid()
            ]);
        }

        $problem->setIsMaintenance(!$problem->isMaintenance());
        $problemRepository->persist($problem);

        $this->addFlash('success', 'problems.maintenance.success');

        return $this->redirectToRoute('show_problem', [
            'uuid' => $problem->getUuid()
        ]);
    }
}