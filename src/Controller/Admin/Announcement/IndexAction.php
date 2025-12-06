<?php

namespace App\Controller\Admin\Announcement;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\PaginationQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    public function __construct(private readonly AnnouncementRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements', name: 'admin_announcements')]
    public function __invoke(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/announcements/index.html.twig', [
            'announcements' => $this->repository->findAllPaginated(new PaginationQuery($page))
        ]);
    }
}