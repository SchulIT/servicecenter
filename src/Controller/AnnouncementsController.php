<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementsController extends Controller {

    private $datehelper;

    public function __construct(DateHelper $dateHelper) {
        $this->datehelper = $dateHelper;
    }

    /**
     * @Route("/announcements", name="announcements")
     */
    public function index(AnnouncementCategoryRepositoryInterface $announcementCategoryRepository) {
        $categories = $announcementCategoryRepository
            ->findAllWithCurrentAnnouncements($this->datehelper->getToday());

        return $this->render('announcements/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/announcements/{id}", name="show_announcement")
     */
    public function show(Announcement $announcement) {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}