<?php

namespace App\Helper\Status;

use App\Entity\Device;
use App\Entity\Problem;

class CurrentDeviceStatus {
    private array $problems = [ ];
    private array $maintenance = [ ];

    public function __construct(private Device $device)
    {
    }

    public function addProblem(Problem $problem) {
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
    public function getDevice() {
        return $this->device;
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