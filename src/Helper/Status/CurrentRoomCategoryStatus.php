<?php

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\Problem;
use App\Entity\Room;
use App\Entity\RoomCategory;

class CurrentRoomCategoryStatus {
    private $category;
    private $rooms = [ ];

    private $problems = [ ];
    private $maintenance = [ ];
    private $announcements = [ ];

    public function __construct(RoomCategory $category) {
        $this->category = $category;
    }

    /**
     * @param Room $room
     * @param Announcement[] $announcements
     * @return CurrentRoomStatus
     */
    public function addRoom(Room $room, array $announcements) {
        $roomStatus = new CurrentRoomStatus($room);

        foreach($room->getDevices() as $device) {
            $roomStatus->addDevice($device);

            $this->problems += $roomStatus->getProblems();
            $this->maintenance += $roomStatus->getMaintenance();
        }

        foreach($announcements as $announcement) {
            $roomStatus->addAnnouncement($announcement);

            $this->announcements = array_merge($this->announcements, $roomStatus->getAnnouncements());
        }

        $this->rooms[] = $roomStatus;

        return $roomStatus;
    }

    /**
     * @return RoomCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @return Announcement[]
     */
    public function getAnnouncements() {
        return $this->announcements;
    }

    /**
     * @return CurrentRoomStatus[]
     */
    public function getRoomStatuses() {
        return $this->rooms;
    }

    /**
     * @return Problem[]
     */
    public function getProblems() {
        return $this->problems;
    }

    /**
     * @return int
     */
    public function getProblemCount() {
        return count($this->problems);
    }

    /**
     * @return Problem[]
     */
    public function getMaintenance() {
        return $this->maintenance;
    }

    /**
     * @return int
     */
    public function getMaintenanceCount() {
        return count($this->maintenance);
    }
}