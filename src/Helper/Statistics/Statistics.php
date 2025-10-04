<?php

declare(strict_types=1);

namespace App\Helper\Statistics;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Statistics {
    public const string MODE_ROOMS = 'rooms';
    public const string MODE_TYPES = 'types';

    #[Assert\Length(min: 1)]
    private ArrayCollection $rooms;

    #[Assert\Length(min: 1)]
    private ArrayCollection $types;

    #[Assert\NotNull]
    private ?DateTime $start = null;

    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    private ?DateTime $end = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['rooms', 'types'])]
    private string $mode = self::MODE_ROOMS;

    private bool $includeMaintenance = false;

    private bool $includeSolved = false;

    public function __construct() {
        $this->rooms = new ArrayCollection();
        $this->types = new ArrayCollection();
    }

    public function getRooms(): ArrayCollection {
        return $this->rooms;
    }

    public function setRooms(ArrayCollection $rooms): Statistics {
        $this->rooms = $rooms;
        return $this;
    }

    public function getTypes(): ArrayCollection {
        return $this->types;
    }

    public function setTypes(ArrayCollection $types): Statistics {
        $this->types = $types;
        return $this;
    }

    public function getStart(): ?DateTime {
        return $this->start;
    }

    public function setStart(DateTime $start): Statistics {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?DateTime {
        return $this->end;
    }

    public function setEnd(DateTime $end): Statistics {
        $this->end = $end;
        return $this;
    }

    public function isIncludeMaintenance(): bool {
        return $this->includeMaintenance;
    }

    public function setIncludeMaintenance(bool $includeMaintenance): Statistics {
        $this->includeMaintenance = $includeMaintenance;
        return $this;
    }

    public function isIncludeSolved(): bool {
        return $this->includeSolved;
    }

    public function setIncludeSolved(bool $includeSolved): Statistics {
        $this->includeSolved = $includeSolved;
        return $this;
    }

    public function getMode(): string {
        return $this->mode;
    }

    public function setMode(string $mode): Statistics {
        $this->mode = $mode;
        return $this;
    }
}
