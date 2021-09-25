<?php

namespace App\Helper\Statistics;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Statistics {
    const MODE_ROOMS = 'rooms';
    const MODE_TYPES = 'types';

    /**
     * @var ArrayCollection
     * @Assert\Length(min="1")
     */
    private $rooms = [ ];

    /**
     * @var ArrayCollection
     * @Assert\Length(min="1")
     */
    private $types = [ ];

    /**
     * @var \DateTime
     * @Assert\NotNull()
     */
    private $start = null;

    /**
     * @var \DateTime
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(propertyPath="start")
     */
    private $end = null;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"rooms", "types"})
     */
    private $mode = self::MODE_ROOMS;

    /**
     * @var boolean
     */
    private $includeMaintenance = false;

    /**
     * @var boolean
     */
    private $includeSolved = false;

    public function __construct() {
        $this->rooms = new ArrayCollection();
        $this->types = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getRooms(): ArrayCollection {
        return $this->rooms;
    }

    /**
     * @param ArrayCollection $rooms
     * @return Statistics
     */
    public function setRooms(ArrayCollection $rooms): Statistics {
        $this->rooms = $rooms;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTypes(): ArrayCollection {
        return $this->types;
    }

    /**
     * @param ArrayCollection $types
     * @return Statistics
     */
    public function setTypes(ArrayCollection $types): Statistics {
        $this->types = $types;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?\DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     * @return Statistics
     */
    public function setStart(\DateTime $start): Statistics {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     * @return Statistics
     */
    public function setEnd(\DateTime $end): Statistics {
        $this->end = $end;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeMaintenance(): bool {
        return $this->includeMaintenance;
    }

    /**
     * @param bool $includeMaintenance
     * @return Statistics
     */
    public function setIncludeMaintenance(bool $includeMaintenance): Statistics {
        $this->includeMaintenance = $includeMaintenance;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeSolved(): bool {
        return $this->includeSolved;
    }

    /**
     * @param bool $includeSolved
     * @return Statistics
     */
    public function setIncludeSolved(bool $includeSolved): Statistics {
        $this->includeSolved = $includeSolved;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string {
        return $this->mode;
    }

    /**
     * @param string $mode
     * @return Statistics
     */
    public function setMode(string $mode): Statistics {
        $this->mode = $mode;
        return $this;
    }
}