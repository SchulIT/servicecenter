<?php

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

    public function __construct(private Room $room)
    {
    }

    /**
     * @param Problem[] $problems
     * @return CurrentDeviceTypeStatus
     */
    public function addDevice(Device $device, array $problems) {
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

    public function addAnnouncement(Announcement $announcement) {
        $this->announcements[] = $announcement;
    }

    /**
     * @return CurrentDeviceTypeStatus[]
     */
    public function getDeviceTypeStatuses() {
        return $this->deviceTypes;
    }

    public function getRoom() {
        return $this->room;
    }

    public function getProblems() {
        return $this->problems;
    }

    public function getProblemCount() {
        return count($this->problems);
    }

    public function getMaintenance() {
        return $this->maintenance;
    }

    public function getMaintenanceCount() {
        return count($this->maintenance);
    }

    public function getAnnouncements() {
        return $this->announcements;
    }

    public function getAnnouncementCount() {
        return count($this->announcements);
    }
}