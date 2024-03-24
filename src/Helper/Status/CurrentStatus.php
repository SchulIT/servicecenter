<?php

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\Problem;
use App\Entity\RoomCategory;

class CurrentStatus {
    private array $problems = [ ];
    private array $maintenance = [ ];
    private array $announcements = [ ];

    private array $categories = [ ];

    /**
     * @param Problem[] $problems
     * @param Announcement[] $announcements
     */
    public function addRoomCategory(RoomCategory $roomCategory, array $problems, array $announcements) {
        $categoryStatus = new CurrentRoomCategoryStatus($roomCategory);

        foreach($roomCategory->getRooms() as $room) {
            $roomProblems = array_filter($problems, fn(Problem $problem) => $problem->getDevice()->getRoom()->getId() === $room->getId());

            $roomAnnouncements = array_filter($announcements, fn(Announcement $announcement) => $announcement->getRooms()->contains($room));

            $roomStatus = $categoryStatus->addRoom($room, $roomProblems, $roomAnnouncements);

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