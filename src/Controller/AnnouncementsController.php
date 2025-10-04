<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Announcement;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementsController extends AbstractController {

    public function __construct(private readonly DateHelper $datehelper)
    {
    }

    #[Route(path: '/announcements', name: 'announcements')]
    public function index(AnnouncementCategoryRepositoryInterface $announcementCategoryRepository): Response {
        $categories = $announcementCategoryRepository
            ->findAllWithCurrentAnnouncements($this->datehelper->getToday());

        return $this->render('announcements/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/announcements/{uuid}', name: 'show_announcement')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): Response {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}
