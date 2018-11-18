<?php

namespace App\Helper\Status;

use App\Entity\Announcement;
use App\Entity\Device;
use App\Entity\Room;

class CurrentRoomStatus {
    private $room;

    private $deviceTypes = [ ];

    private $problems = [ ];
    private $maintenance = [ ];
    private $announcements = [ ];

    public function __construct(Room $room) {
        $this->room = $room;
    }

    /**
     * @param Device $device
     * @return CurrentDeviceTypeStatus
     */
    public function addDevice(Device $device) {
        $deviceType = $device->getType();
        $id = $deviceType->getId();

        if(!isset($this->deviceTypes[$id])) {
            $this->deviceTypes[$id] = new CurrentDeviceTypeStatus($deviceType);
        }

        $deviceTypeStatus = $this->deviceTypes[$id];
        $deviceTypeStatus->addDevice($device);

        $this->problems = array_merge($this->problems, $deviceTypeStatus->getProblems());
        $this->maintenance = array_merge($this->maintenance, $deviceTypeStatus->getMaintenance());

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