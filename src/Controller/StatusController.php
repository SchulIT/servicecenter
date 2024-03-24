<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Announcement;
use App\Entity\Device;
use App\Entity\Room;
use App\Helper\Status\CurrentStatusHelper;
use App\Repository\AnnouncementRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController {
    public function __construct(private CurrentStatusHelper $currentStatusHelper)
    {
    }

    #[Route(path: '/status', name: 'current_status')]
    public function index(): Response
    {
        $status = $this->currentStatusHelper->getCurrentStatus();
        return $this->render('status/index.html.twig', [
            'status' => $status
        ]);
    }

    #[Route(path: '/status/r/{uuid}', name: 'status_room')]
    public function roomStatus(Room $room): Response {
        $status = $this->currentStatusHelper->getCurrentStatusForRoom($room);

        return $this->render('status/room.html.twig', [
            'room' => $room,
            'status' => $status
        ]);
    }

    #[Route(path: '/status/d/{uuid}', name: 'status_device')]
    public function deviceStatus(Device $device, DateHelper $dateHelper, AnnouncementRepositoryInterface $announcementRepository): Response {
        $status = $this->currentStatusHelper->getCurrentStatusForDevice($device);

        /** @var Announcement[] $announcements */
        $announcements = $announcementRepository
            ->findActiveByRoom($device->getRoom(), $dateHelper->getToday());


        return $this->render('status/device.html.twig', [
            'device' => $device,
            'status' => $status,
            'announcements' => $announcements
        ]);
    }
}