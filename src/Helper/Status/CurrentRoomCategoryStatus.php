<?php

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\Problem;
use App\Entity\Room;
use App\Entity\RoomCategory;

class CurrentRoomCategoryStatus {
    private array $rooms = [ ];

    private array $problems = [ ];
    private array $maintenance = [ ];
    private array $announcements = [ ];

    public function __construct(private readonly RoomCategory $category)
    {
    }

    /**
     * @param Problem[] $problems
     * @param Announcement[] $announcements
     * @return CurrentRoomStatus
     */
    public function addRoom(Room $room, array $problems, array $announcements): CurrentRoomStatus {
        $roomStatus = new CurrentRoomStatus($room);

        foreach($room->getDevices() as $device) {
            $deviceProblems = array_filter($problems, fn(Problem $problem) => $problem->getDevice()->getId() === $device->getId());

            $roomStatus->addDevice($device, $deviceProblems);

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
    public function getCategory(): RoomCategory {
        return $this->category;
    }

    /**
     * @return Announcement[]
     */
    public function getAnnouncements(): array {
        return $this->announcements;
    }

    /**
     * @return CurrentRoomStatus[]
     */
    public function getRoomStatuses(): array {
        return $this->rooms;
    }

    /**
     * @return Problem[]
     */
    public function getProblems(): array {
        return $this->problems;
    }

    /**
     * @return int
     */
    public function getProblemCount(): int {
        return count($this->problems);
    }

    /**
     * @return Problem[]
     */
    public function getMaintenance(): array {
        return $this->maintenance;
    }

    /**
     * @return int
     */
    public function getMaintenanceCount(): int {
        return count($this->maintenance);
    }
}