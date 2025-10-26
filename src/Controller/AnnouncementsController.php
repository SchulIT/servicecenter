<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\PaginationQuery;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Announcement;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementsController extends AbstractController {

    public function __construct(private readonly DateHelper $datehelper)
    {
    }

    #[Route(path: '/announcements', name: 'announcements')]
    public function index(
        AnnouncementRepositoryInterface $repository,
        #[MapQueryParameter] int $page = 1
    ): Response {
        $announcements = $repository->findAllActivePaginated(new PaginationQuery(page: $page), $this->datehelper->getToday());

        return $this->render('announcements/index.html.twig', [
            'announcements' => $announcements
        ]);
    }

    #[Route(path: '/announcements/{uuid}', name: 'show_announcement')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): Response {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}
