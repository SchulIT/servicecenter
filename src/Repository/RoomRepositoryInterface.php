<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\ORM\QueryBuilder;

interface RoomRepositoryInterface {

    /**
     * @param int $id
     * @return Room|null
     */
    public function findOneById(int $id): ?Room;

    /**
     * @param string $uuid
     * @return Room|null
     */
    public function findOneByUuid(string $uuid): ?Room;

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