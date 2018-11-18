<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\Room;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;

class CurrentStatusHelper {
    private $roomCategoryRepository;
    private $roomRepository;
    private $deviceRepository;

    public function __construct(RoomCategoryRepositoryInterface $roomCategoryRepository, RoomRepositoryInterface $roomRepository,
                                DeviceRepositoryInterface $deviceRepository) {
        $this->roomCategoryRepository = $roomCategoryRepository;
        $this->roomRepository = $roomRepository;
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @return CurrentStatus
     */
    public function getCurrentStatus() {
        $status = new CurrentStatus();

        $categories = $this->roomCategoryRepository->findWithOpenProblems();

        foreach($categories as $category) {
            $status->addRoomCategory($category);
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

        foreach($room->getAnnouncements() as $announcement) {
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