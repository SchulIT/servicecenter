<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\Room;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\ProblemRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

class CurrentStatusHelper {
    public function __construct(private readonly RoomCategoryRepositoryInterface $roomCategoryRepository, private readonly AnnouncementRepositoryInterface $announcementRepository, private readonly ProblemRepositoryInterface $problemRepository, private readonly DateHelper $dateHelper)
    {
    }

    public function getCurrentStatus(): CurrentStatus {
        $status = new CurrentStatus();

        $categories = $this->roomCategoryRepository->findAll();
        $problems = $this->problemRepository->findOpen();
        $announcements = $this->announcementRepository->findActive($this->dateHelper->getToday());

        foreach($categories as $category) {
            $status->addRoomCategory($category, $problems, $announcements);
        }

        return $status;
    }

    public function getCurrentStatusForRoom(Room $room): CurrentRoomStatus {
        $status = new CurrentRoomStatus($room);

        $problems = $this->problemRepository->findOpenByRoom($room);

        foreach($room->getDevices() as $device) {
            $deviceProblems = array_filter($problems, fn(Problem $problem) => $problem->getDevice()->getId() === $device->getId());

            $status->addDevice($device, $deviceProblems);
        }

        $announcements = $this->announcementRepository->findActiveByRoom($room, $this->dateHelper->getToday());

        foreach($announcements as $announcement) {
            $status->addAnnouncement($announcement);
        }

        return $status;
    }

    public function getCurrentStatusForDevice(Device $device): CurrentDeviceStatus {
        $status = new CurrentDeviceStatus($device);

        $problems = $this->problemRepository->findOpenByDevice($device);

        foreach($problems as $problem) {
            $status->addProblem($problem);
        }

        return $status;
    }
}