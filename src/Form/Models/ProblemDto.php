<?php

namespace App\Form\Models;

use App\Entity\Device;
use App\Entity\Priority;
use App\Entity\ProblemType;
use App\Validator\SameProblemType;
use Symfony\Component\Validator\Constraints as Assert;

class ProblemDto {

    /**
     * @Assert\NotNull()
     * @var ProblemType|null
     */
    private $problemType;

    /**
     * @Assert\NotNull()
     * @var Priority
     */
    private $priority;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $content;

    /**
     * @Assert\Count(min="1")
     * @SameProblemType()
     * @var Device[]
     */
    private $devices = [ ];

    public function __construct() {
        $this->priority = Priority::Normal();
    }

    /**
     * @return ProblemType|null
     */
    public function getProblemType(): ?ProblemType {
        return $this->problemType;
    }

    /**
     * @param ProblemType|null $problemType
     * @return ProblemDto
     */
    public function setProblemType(?ProblemType $problemType): ProblemDto {
        $this->problemType = $problemType;
        return $this;
    }

    /**
     * @return Priority
     */
    public function getPriority(): Priority {
        return $this->priority;
    }

    /**
     * @param Priority $priority
     * @return ProblemDto
     */
    public function setPriority(Priority $priority): ProblemDto {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return ProblemDto
     */
    public function setContent(?string $content): ProblemDto {
        $this->content = $content;
        return $this;
    }

    /**
     * @return Device[]
     */
    public function getDevices(): iterable {
        return $this->devices;
    }

    /**
     * @param Device[] $devices
     * @return ProblemDto
     */
    public function setDevices(iterable $devices): ProblemDto {
        $this->devices = $devices;
        return $this;
    }
}