<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\Problem;

class CurrentDeviceStatus {
    private array $problems = [ ];
    private array $maintenance = [ ];

    public function __construct(private readonly Device $device)
    {
    }

    public function addProblem(Problem $problem): void {
        $id = $problem->getId();

        if($problem->isMaintenance()) {
            $this->maintenance[$id] = $problem;
        } else {
            $this->problems[$id] = $problem;
        }
    }

    /**
     * @return Device
     */
    public function getDevice(): Device {
        return $this->device;
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