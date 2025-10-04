<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Helper\Status\CurrentStatusHelper;
use App\Response\Room;
use App\Response\Status;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController extends AbstractController
{
    #[Route(path: '/api/status', name: 'api_status', methods: ['GET'])]
    public function status(CurrentStatusHelper $currentStatusHelper, UrlGeneratorInterface $urlGenerator): JsonResponse {
        $status = $currentStatusHelper->getCurrentStatus();

        $response = new Status();

        foreach($status->getRoomCategoryStatuses() as $roomCategoryStatus) {
            foreach($roomCategoryStatus->getRoomStatuses() as $roomStatus) {
                $response->addRoom(
                    (new Room())
                        ->setName($roomStatus->getRoom()->getName())
                        ->setNumMaintanance($roomStatus->getMaintenanceCount())
                        ->setNumProblems($roomStatus->getProblemCount())
                        ->setLink($urlGenerator->generate('status_room', ['uuid' => $roomStatus->getRoom()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL))
                );
            }
        }

        return $this->json($response);
    }
}
