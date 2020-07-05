<?php

namespace App\Controller;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

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