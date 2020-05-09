<?php

namespace App\Helper\Problems\History;

use App\Entity\User;

class PropertyChangedHistoryItem implements HistoryItemInterface {
    private $property;

    private $dateTime;

    private $user;

    private $username;

    private $newValue;

    private $text;

    public function __construct(string $property, \DateTime $dateTime, ?User $user, string $username, $newValue, string $text) {
        $this->property = $property;
        $this->dateTime = $dateTime;
        $this->user = $user;
        $this->username = $username;
        $this->newValue = $newValue;
        $this->text = $text;
    }

    public function getProperty(): string {
        return $this->property;
    }

    public function getDateTime(): \DateTime {
        return $this->dateTime;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getNewValue() {
        return $this->newValue;
    }

    public function getText(): string {
        return $this->text;
    }
}