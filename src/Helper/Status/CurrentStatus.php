<?php

declare(strict_types=1);

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
    public function addRoomCategory(RoomCategory $roomCategory, array $problems, array $announcements): void {
        $categoryStatus = new CurrentRoomCategoryStatus($roomCategory);

        foreach($roomCategory->getRooms() as $room) {
            $roomProblems = array_filter($problems, fn(Problem $problem): bool => $problem->getDevice()->getRoom()->getId() === $room->getId());

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
    public function getRoomCategoryStatuses(): array {
        return $this->categories;
    }

    public function getProblemCount(): int {
        return count($this->problems);
    }

    public function getMaintenanceCount(): int {
        return count($this->maintenance);
    }

    public function getAnnouncementCount(): int {
        return count($this->announcements);
    }
}
