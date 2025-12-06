<?php

namespace App\Controller\Application;

use App\Repository\ApplicationRepositoryInterface;
use App\Repository\PaginationQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    #[Route(path: '/admin/applications', name: 'applications')]
    public function __invoke(
        ApplicationRepositoryInterface $repository,
        #[MapQueryParameter] int $page = 1
    ): Response {
        return $this->render('admin/applications/index.html.twig', [
            'applications' => $repository->findAllPaginated(new PaginationQuery(page: $page))
        ]);
    }
}