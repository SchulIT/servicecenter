<?php

namespace App\Response;

use DateTime;

class Status {

    /** @var DateTime */
    private $now;

    /** @var Room[] */
    private $rooms = [ ];

    public function __construct() {
        $this->now = new DateTime();
    }

    public function addRoom(Room $room): void {
        $this->rooms[] = $room;
    }

    /**
     * @return DateTime
     */
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