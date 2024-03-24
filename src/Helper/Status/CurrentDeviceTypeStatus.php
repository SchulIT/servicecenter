<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Problem;

class CurrentDeviceTypeStatus {
    private ?array $devices = null;

    private array $problems = [ ];
    private array $maintenance = [ ];

    public function __construct(private DeviceType $deviceType)
    {
    }

    /**
     * @param Problem[] $problems
     * @return CurrentDeviceStatus
     */
    public function addDevice(Device $device, array $problems) {
        $deviceStatus = new CurrentDeviceStatus($device);

        foreach($problems as $problem) {
            $deviceStatus->addProblem($problem);
        }

        $this->problems += $deviceStatus->getProblems();
        $this->maintenance += $deviceStatus->getMaintenance();

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