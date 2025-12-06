<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Repository\PaginationQuery;
use App\Repository\ProblemRepositoryInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class RelatedAction extends AbstractController {
    #[Route('/problems/{uuid}/related', name: 'related_problems')]
    public function __invoke(
        ProblemRepositoryInterface $problemRepository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        #[MapQueryParameter] int $page = 1
    ): Response {
        $relatedProblems = $problemRepository->findRelatedPaginated(new PaginationQuery(page: $page), $problem);

        return $this->render('problems/related.html.twig', [
            'problem' => $problem,
            'relatedProblems' => $relatedProblems
        ]);
    }
}