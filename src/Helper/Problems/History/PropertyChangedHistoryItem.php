<?php

namespace App\Helper\Problems\History;

use DateTime;
use App\Entity\User;

class PropertyChangedHistoryItem implements HistoryItemInterface {
    public function __construct(private readonly string $property, private readonly DateTime $dateTime, private readonly ?User $user, private readonly string $username, private $newValue, private readonly string $text)
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