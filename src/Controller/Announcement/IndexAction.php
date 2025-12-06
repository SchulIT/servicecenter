<?php

namespace App\Controller\Announcement;

use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\PaginationQuery;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    #[Route(path: '/announcements', name: 'announcements')]
    public function __invoke(
        AnnouncementRepositoryInterface $repository,
        DateHelper $dateHelper,
        #[MapQueryParameter] int $page = 1
    ): Response {
        $announcements = $repository->findAllActivePaginated(new PaginationQuery(page: $page), $dateHelper->getToday());

        return $this->render('announcements/index.html.twig', [
            'announcements' => $announcements
        ]);
    }
}