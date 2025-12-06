<?php

namespace App\Controller\Admin\AnnouncementCategory;

use App\Repository\AnnouncementCategoryRepositoryInterface;
use App\Repository\PaginationQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly AnnouncementCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/categories', name: 'admin_announcementcategories')]
    public function __invoke(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/announcements/categories/index.html.twig', [
            'categories' => $this->repository->findAllPaginated(new PaginationQuery($page))
        ]);
    }
}