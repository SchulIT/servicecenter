<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Announcement;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementsController extends AbstractController {

    public function __construct(private DateHelper $datehelper)
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
    public function show(Announcement $announcement): Response {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}