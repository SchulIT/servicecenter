<?php

declare(strict_types=1);

namespace App\Form\Models;

use App\Entity\Device;
use App\Entity\Priority;
use App\Entity\ProblemType;
use App\Validator\SameProblemType;
use Symfony\Component\Validator\Constraints as Assert;

class ProblemDto {

    #[Assert\NotNull]
    private ?ProblemType $problemType = null;

    #[Assert\NotNull]
    private Priority $priority = Priority::Normal;

    #[Assert\NotBlank]
    private ?string $content = null;

    /**
     * @var Device[]
     */
    #[SameProblemType]
    #[Assert\Count(min: 1)]
    private iterable $devices = [ ];

    public function getProblemType(): ?ProblemType {
        return $this->problemType;
    }

    public function setProblemType(?ProblemType $problemType): ProblemDto {
        $this->problemType = $problemType;
        return $this;
    }

    public function getPriority(): Priority {
        return $this->priority;
    }

    public function setPriority(Priority $priority): ProblemDto {
        $this->priority = $priority;
        return $this;
    }

    public function getContent(): ?string {
        return $this->content;
    }

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
     */
    public function setDevices(iterable $devices): ProblemDto {
        $this->devices = $devices;
        return $this;
    }
}
