<?php

namespace App\Controller\Notification;

use App\Entity\Room;
use App\Helper\Status\CurrentStatusHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoomAction extends AbstractController {
    #[Route(path: '/status/r/{uuid}', name: 'status_room')]
    public function roomStatus(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Room $room,
        CurrentStatusHelper $currentStatusHelper
    ): Response {
        $status = $currentStatusHelper->getCurrentStatusForRoom($room);

        return $this->render('status/room.html.twig', [
            'room' => $room,
            'status' => $status
        ]);
    }
}