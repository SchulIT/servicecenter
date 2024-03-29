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

    public function persist(Room $room): void;

    public function remove(Room $room): void;
}