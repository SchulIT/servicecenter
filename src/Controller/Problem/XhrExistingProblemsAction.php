<?php

namespace App\Controller\Problem;

use App\Repository\ProblemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class XhrExistingProblemsAction extends AbstractController {
    #[Route(path: '/problems/xhr/existing', name: 'existing_problems_ajax')]
    public function __invoke(
        ProblemRepositoryInterface $problemRepository,
        #[MapQueryParameter(name: 'type')] int|null $typeId = null,
        #[MapQueryParameter(name: 'devices', filter: FILTER_VALIDATE_INT)] array $deviceIds = [ ]
    ): Response {
        if(count($deviceIds) === 0) {
            return $this->render('problems/existing.html.twig', [
                'problems' => null
            ]);
        }

        $problems = $problemRepository->findOpenByDeviceIds($deviceIds, $typeId);

        return $this->render('problems/existing.html.twig', [
            'problems' => $problems
        ]);
    }
}