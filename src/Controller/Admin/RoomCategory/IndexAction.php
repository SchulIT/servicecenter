<?php

namespace App\Controller\Admin\RoomCategory;

use App\Repository\PaginationQuery;
use App\Repository\RoomCategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly RoomCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/categories', name: 'admin_roomcategories')]
    public function __invoke(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/rooms/categories/index.html.twig', [
            'categories' => $this->repository->findAllPaginated(new PaginationQuery($page))
        ]);
    }
}