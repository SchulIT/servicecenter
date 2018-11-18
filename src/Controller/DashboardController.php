<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\RoomCategory;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {

    private $datehelper;

    public function __construct(DateHelper $dateHelper) {
        $this->datehelper = $dateHelper;
    }

    /**
     * @Route("/")
     * @Route("/dashboard", name="dashboard")
     */
    public function index(AnnouncementRepositoryInterface $announcementRepository, RoomCategoryRepositoryInterface $roomCategoryRepository) {
        $numAnnouncements = $announcementRepository
            ->countActive($this->datehelper->getToday());

        $roomCategories = $roomCategoryRepository
            ->findAll();

        return $this->render('dashboard/index.html.twig', [
            'numAnnouncements' => $numAnnouncements,
            'roomCategories' => $roomCategories
        ]);
    }
}