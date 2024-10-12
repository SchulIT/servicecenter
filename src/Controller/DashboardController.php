<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController {

    private const NumberOfLatestProblems = 10;

    public function __construct(private readonly DateHelper $datehelper)
    {
    }

    #[Route(path: '/')]
    #[Route(path: '/dashboard', name: 'dashboard')]
    public function index(AnnouncementRepositoryInterface $announcementRepository, RoomCategoryRepositoryInterface $roomCategoryRepository, ProblemRepositoryInterface $problemRepository): Response {
        $announcements = $announcementRepository
            ->findActive($this->datehelper->getToday());

        $roomCategories = $roomCategoryRepository
            ->findAll();

        $latestProblems = $problemRepository->getLatest(self::NumberOfLatestProblems, false);

        return $this->render('dashboard/index.html.twig', [
            'announcements' => $announcements,
            'roomCategories' => $roomCategories,
            'latestProblems' => $latestProblems
        ]);
    }
}