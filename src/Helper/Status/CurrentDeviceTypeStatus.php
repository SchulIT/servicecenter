<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Problem;

class CurrentDeviceTypeStatus {
    private $deviceType;

    private $devices;

    private $problems = [ ];
    private $maintenance = [ ];

    public function __construct(DeviceType $deviceType) {
        $this->deviceType = $deviceType;
    }

    /**
     * @param Device $device
     * @return CurrentDeviceStatus
     */
    public function addDevice(Device $device) {
        $deviceStatus = new CurrentDeviceStatus($device);

        foreach($device->getProblems() as $problem) {
            $deviceStatus->addProblem($problem);

            $this->problems = array_merge($this->problems, $deviceStatus->getProblems());
            $this->maintenance = array_merge($this->maintenance, $deviceStatus->getMaintenance());
        }

        $this->devices[] = $deviceStatus;

        return $deviceStatus;
    }

    /**
     * @return DeviceType
     */
    public function getDeviceType() {
        return $this->deviceType;
    }

    /**
     * @return CurrentDeviceTypeStatus[]
     */
    public function getDeviceStatuses() {
        return $this->devices;
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