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
    private $roomCategoryRepository;
    private $roomRepository;
    private $deviceRepository;
    private $announcementRepository;
    private $problemRepository;
    private $dateHelper;

    public function __construct(RoomCategoryRepositoryInterface $roomCategoryRepository, RoomRepositoryInterface $roomRepository,
                                DeviceRepositoryInterface $deviceRepository, AnnouncementRepositoryInterface $announcementRepository,
                                ProblemRepositoryInterface $problemRepository, DateHelper $dateHelper) {
        $this->roomCategoryRepository = $roomCategoryRepository;
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
        $this->announcementRepository = $announcementRepository;
        $this->problemRepository = $problemRepository;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @return CurrentStatus
     */
    public function getCurrentStatus() {
        $status = new CurrentStatus();

        $categories = $this->roomCategoryRepository->findAll();
        $problems = $this->problemRepository->findOpen();
        $announcements = $this->announcementRepository->findActive($this->dateHelper->getToday());

        foreach($categories as $category) {
            $status->addRoomCategory($category, $problems, $announcements);
        }

        return $status;
    }

    /**
     * @param Room $room
     * @return CurrentRoomStatus
     */
    public function getCurrentStatusForRoom(Room $room) {
        $status = new CurrentRoomStatus($room);

        $problems = $this->problemRepository->findOpenByRoom($room);

        foreach($room->getDevices() as $device) {
            $deviceProblems = array_filter($problems, function(Problem $problem) use ($device) {
                return $problem->getDevice()->getId() === $device->getId();
            });

            $status->addDevice($device, $deviceProblems);
        }

        $announcements = $this->announcementRepository->findActiveByRoom($room, $this->dateHelper->getToday());

        foreach($announcements as $announcement) {
            $status->addAnnouncement($announcement);
        }

        return $status;
    }

    public function getCurrentStatusForDevice(Device $device) {
        $status = new CurrentDeviceStatus($device);

        $problems = $this->problemRepository->findOpenByDevice($device);

        foreach($problems as $problem) {
            $status->addProblem($problem);
        }

        return $status;
    }
}