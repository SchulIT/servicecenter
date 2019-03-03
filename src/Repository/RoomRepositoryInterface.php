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

    public function persist(Room $room);

    public function remove(Room $room);
}