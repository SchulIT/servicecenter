<?php

namespace App\Controller\Admin\Room;

use App\Repository\PaginationQuery;
use App\Repository\RoomRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly RoomRepositoryInterface $repository) {
    }

    #[Route(path: '/admin/rooms', name: 'admin_rooms')]
    public function index(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/rooms/index.html.twig', [
            'rooms' => $this->repository->findAllPaginated(new PaginationQuery($page))
        ]);
    }
}