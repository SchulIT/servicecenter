<?php

namespace App\Controller;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {

    private const NumberOfLatestProblems = 10;

    private $datehelper;

    public function __construct(DateHelper $dateHelper) {
        $this->datehelper = $dateHelper;
    }

    /**
     * @Route("/")
     * @Route("/dashboard", name="dashboard")
     */
    public function index(AnnouncementRepositoryInterface $announcementRepository, RoomCategoryRepositoryInterface $roomCategoryRepository, ProblemRepositoryInterface $problemRepository) {
        $announcements = $announcementRepository
            ->findActive($this->datehelper->getToday());

        $roomCategories = $roomCategoryRepository
            ->findAll();

        $latestProblems = $problemRepository->getLatest(static::NumberOfLatestProblems);

        return $this->render('dashboard/index.html.twig', [
            'announcements' => $announcements,
            'roomCategories' => $roomCategories,
            'latestProblems' => $latestProblems
        ]);
    }
}