<?php

namespace App\Response;

class Room {

    private ?string $name = null;

    private ?string $link = null;

    private int $numProblems = 0;

    private int $numMaintanance = 0;

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): Room {
        $this->name = $name;
        return $this;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function setLink(string $link): Room {
        $this->link = $link;
        return $this;
    }

    public function getNumProblems(): int {
        return $this->numProblems;
    }

    public function getNumMaintanance(): int {
        return $this->numMaintanance;
    }

    public function setNumProblems(int $numProblems): Room {
        $this->numProblems = $numProblems;
        return $this;
    }

    public function setNumMaintanance(int $numMaintanance): Room {
        $this->numMaintanance = $numMaintanance;
        return $this;
    }
}