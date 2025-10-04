<?php

declare(strict_types=1);

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\Room;

class CurrentRoomStatus {
    private array $deviceTypes = [ ];

    private array $problems = [ ];
    private array $maintenance = [ ];
    private array $announcements = [ ];

    public function __construct(private readonly Room $room)
    {
    }

    /**
     * @param Problem[] $problems
     */
    public function addDevice(Device $device, array $problems): CurrentDeviceTypeStatus {
        $deviceType = $device->getType();
        $id = $deviceType->getId();

        if(!isset($this->deviceTypes[$id])) {
            $this->deviceTypes[$id] = new CurrentDeviceTypeStatus($deviceType);
        }

        $deviceTypeStatus = $this->deviceTypes[$id];
        $deviceTypeStatus->addDevice($device, $problems);

        $this->problems += $deviceTypeStatus->getProblems();
        $this->maintenance += $deviceTypeStatus->getMaintenance();

        return $deviceTypeStatus;
    }

    public function addAnnouncement(Announcement $announcement): void {
        $this->announcements[] = $announcement;
    }

    /**
     * @return CurrentDeviceTypeStatus[]
     */
    public function getDeviceTypeStatuses(): array {
        return $this->deviceTypes;
    }

    public function getRoom(): Room {
        return $this->room;
    }

    public function getProblems(): array {
        return $this->problems;
    }

    public function getProblemCount(): int {
        return count($this->problems);
    }

    public function getMaintenance(): array {
        return $this->maintenance;
    }

    public function getMaintenanceCount(): int {
        return count($this->maintenance);
    }

    public function getAnnouncements(): array {
        return $this->announcements;
    }

    public function getAnnouncementCount(): int {
        return count($this->announcements);
    }
}
