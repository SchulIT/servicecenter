<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\Room;
use App\Repository\AnnouncementRepositoryInterface;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;

class CurrentStatusHelper {
    private $roomCategoryRepository;
    private $roomRepository;
    private $deviceRepository;
    private $announcementRepository;
    private $dateHelper;

    public function __construct(RoomCategoryRepositoryInterface $roomCategoryRepository, RoomRepositoryInterface $roomRepository,
                                DeviceRepositoryInterface $deviceRepository, AnnouncementRepositoryInterface $announcementRepository,
                                DateHelper $dateHelper) {
        $this->roomCategoryRepository = $roomCategoryRepository;
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
        $this->announcementRepository = $announcementRepository;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @return CurrentStatus
     */
    public function getCurrentStatus() {
        $status = new CurrentStatus();

        $categories = $this->roomCategoryRepository->findWithOpenProblems();
        $announcements = $this->announcementRepository->findActive($this->dateHelper->getToday());

        foreach($categories as $category) {
            $status->addRoomCategory($category, $announcements);
        }

        return $status;
    }

    /**
     * @param Room $room
     * @return CurrentRoomStatus
     */
    public function getCurrentStatusForRoom(Room $room) {
        $status = new CurrentRoomStatus($room);

        $room = $this->roomRepository->getRoomWithUnsolvedProblems($room);

        foreach($room->getDevices() as $device) {
            $status->addDevice($device);
        }

        $announcements = $this->announcementRepository->findActiveByRoom($room, $this->dateHelper->getToday());

        foreach($announcements as $announcement) {
            $status->addAnnouncement($announcement);
        }

        return $status;
    }

    public function getCurrentStatusForDevice(Device $device) {
        $device = $this->deviceRepository->getDeviceWithUnresolvedProblems($device);

        $status = new CurrentDeviceStatus($device);

        foreach($device->getProblems() as $problem) {
            $status->addProblem($problem);
        }

        return $status;
    }
}