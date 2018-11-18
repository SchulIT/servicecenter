<?php

namespace App\Helper\Status;

use App\Entity\RoomCategory;

class CurrentStatus {
    private $problems = [ ];
    private $maintenance = [ ];
    private $announcements = [ ];

    private $categories = [ ];

    /**
     * @param RoomCategory $roomCategory
     */
    public function addRoomCategory(RoomCategory $roomCategory) {
        $categoryStatus = new CurrentRoomCategoryStatus($roomCategory);

        foreach($roomCategory->getRooms() as $room) {
            $roomStatus = $categoryStatus->addRoom($room);

            $this->problems = array_merge($this->problems, $roomStatus->getProblems());
            $this->maintenance = array_merge($this->maintenance, $roomStatus->getMaintenance());
            $this->announcements = array_merge($this->announcements, $roomStatus->getAnnouncements());
        }

        $this->categories[] = $categoryStatus;
    }

    /**
     * @return CurrentRoomCategoryStatus[]
     */
    public function getRoomCategoryStatuses() {
        return $this->categories;
    }

    /**
     * @return int
     */
    public function getProblemCount() {
        return count($this->problems);
    }

    /**
     * @return int
     */
    public function getMaintenanceCount() {
        return count($this->maintenance);
    }

    /**
     * @return int
     */
    public function getAnnouncementCount() {
        return count($this->announcements);
    }
}