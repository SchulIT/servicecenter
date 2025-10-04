<?php

declare(strict_types=1);

namespace App\Response;

use DateTime;

class Status {

    private readonly DateTime $now;

    /** @var Room[] */
    private array $rooms = [ ];

    public function __construct() {
        $this->now = new DateTime();
    }

    public function addRoom(Room $room): void {
        $this->rooms[] = $room;
    }

    public function getNow(): DateTime {
        return $this->now;
    }

    /**
     * @return Room[]
     */
    public function getRooms(): array {
        return $this->rooms;
    }
}
