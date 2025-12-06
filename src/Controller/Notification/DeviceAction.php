<?php

namespace App\Controller\Notification;

use App\Entity\Device;
use App\Helper\Status\CurrentStatusHelper;
use App\Repository\AnnouncementRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeviceAction extends AbstractController {
    #[Route(path: '/status/d/{uuid}', name: 'status_device')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Device $device,
        DateHelper $dateHelper,
        AnnouncementRepositoryInterface $announcementRepository,
        CurrentStatusHelper $currentStatusHelper
    ): Response {
        $status = $currentStatusHelper->getCurrentStatusForDevice($device);

        $announcements = $announcementRepository
            ->findActiveByRoom($device->getRoom(), $dateHelper->getToday());


        return $this->render('status/device.html.twig', [
            'device' => $device,
            'status' => $status,
            'announcements' => $announcements
        ]);
    }
}