<?php

namespace App\Repository;

use App\Entity\Placard;
use App\Entity\Room;

interface PlacardRepositoryInterface {

    /**
     * @return Placard[]
     */
    public function findAll();

    /**
     * @param Room $room
     * @return Placard
     */
    public function findOneByRoom(Room $room);
}