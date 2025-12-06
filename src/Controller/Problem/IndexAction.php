<?php

namespace App\Controller\Problem;

use App\Repository\PaginationQuery;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    #[Route(path: '/problems', name: 'problems')]
    public function __invoke(
        ProblemRepositoryInterface $problemRepository,
        RoomRepositoryInterface $roomRepository,
        RoomCategoryRepositoryInterface $roomCategoryRepository,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'room')] string|null $roomUuid = null,
        #[MapQueryParameter] string|null $query = null,
        #[MapQueryParameter(name: 'closed')] bool|null $includeClosed = false
    ): RedirectResponse|Response {
        $room = null;

        if($roomUuid !== null) {
            $room = $roomRepository->findOneByUuid($roomUuid);
        }

        return $this->render('problems/index.html.twig', [
            'problems' => $problemRepository->findAllPaginated(new PaginationQuery(page: $page), room: $room, query: $query, onlyOpen: !$includeClosed),
            'categories' => $roomCategoryRepository->findAll(),
            'room' => $room,
            'query' => $query,
            'includeClosed' => $includeClosed,
            'csrfTokenBulkId' => BulkAction::BULK_CSRF_TOKEN_ID
        ]);
    }
}