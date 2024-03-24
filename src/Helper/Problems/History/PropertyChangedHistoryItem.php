<?php

namespace App\Helper\Problems\History;

use DateTime;
use App\Entity\User;

class PropertyChangedHistoryItem implements HistoryItemInterface {
    public function __construct(private string $property, private DateTime $dateTime, private ?User $user, private string $username, private $newValue, private string $text)
    {
    }

    public function getProperty(): string {
        return $this->property;
    }

    public function getDateTime(): DateTime {
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