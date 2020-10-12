<?php

namespace App\Response;

use App\Entity\Problem;

class Room {

    /** @var string */
    private $name;

    /** @var string */
    private $link;

    /** @var int */
    private $numProblems = 0;

    /** @var int */
    private $numMaintanance = 0;

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Room
     */
    public function setName(string $name): Room {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Room
     */
    public function setLink(string $link): Room {
        $this->link = $link;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumProblems(): int {
        return $this->numProblems;
    }

    /**
     * @return int
     */
    public function getNumMaintanance(): int {
        return $this->numMaintanance;
    }

    /**
     * @param int $numProblems
     * @return Room
     */
    public function setNumProblems(int $numProblems): Room {
        $this->numProblems = $numProblems;
        return $this;
    }

    /**
     * @param int $numMaintanance
     * @return Room
     */
    public function setNumMaintanance(int $numMaintanance): Room {
        $this->numMaintanance = $numMaintanance;
        return $this;
    }
}