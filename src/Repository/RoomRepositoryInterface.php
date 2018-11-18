<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\ORM\QueryBuilder;

interface RoomRepositoryInterface {
    /**
     * @return QueryBuilder
     */
    public function getQueryBuilderForRoomsWithoutPlacard();

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilderForRoomsWithPlacard();

    /**
     * @param Room $room
     * @return Room
     */
    public function getRoomWithUnsolvedProblems(Room $room);
}