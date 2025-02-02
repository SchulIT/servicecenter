<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Problem;

class CurrentDeviceTypeStatus {
    private ?array $devices = null;

    private array $problems = [ ];
    private array $maintenance = [ ];

    public function __construct(private readonly DeviceType $deviceType)
    {
    }

    /**
     * @param Problem[] $problems
     * @return CurrentDeviceStatus
     */
    public function addDevice(Device $device, array $problems): CurrentDeviceStatus {
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
    public function getDeviceType(): DeviceType {
        return $this->deviceType;
    }

    /**
     * @return CurrentDeviceTypeStatus[]
     */
    public function getDeviceStatuses(): ?array {
        return $this->devices;
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