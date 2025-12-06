<?php

namespace App\Controller\Dashboard;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    private const int NumberOfLatestProblems = 10;

    #[Route(path: '/dashboard', name: 'dashboard')]
    public function __invoke(
        AnnouncementRepositoryInterface $announcementRepository,
        RoomCategoryRepositoryInterface $roomCategoryRepository,
        ProblemRepositoryInterface $problemRepository,
        DateHelper $dateHelper
    ): Response {
        $announcements = $announcementRepository
            ->findActive($dateHelper->getToday());

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