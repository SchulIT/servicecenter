<?php

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\RoomCategory;

class CurrentStatus {
    private $problems = [ ];
    private $maintenance = [ ];
    private $announcements = [ ];

    private $categories = [ ];

    /**
     * @param RoomCategory $roomCategory
     * @param Announcement[] $announcements
     */
    public function addRoomCategory(RoomCategory $roomCategory, array $announcements) {
        $categoryStatus = new CurrentRoomCategoryStatus($roomCategory);

        foreach($roomCategory->getRooms() as $room) {
            $roomAnnouncements = array_filter($announcements, function(Announcement $announcement) use ($room) {
                return $announcement->getRooms()->contains($room);
            });

            $roomStatus = $categoryStatus->addRoom($room, $roomAnnouncements);

            $this->problems += $roomStatus->getProblems();
            $this->maintenance += $roomStatus->getMaintenance();
            $this->announcements += $roomStatus->getAnnouncements();
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