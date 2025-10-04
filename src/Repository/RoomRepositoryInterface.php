<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Room;
use Doctrine\ORM\QueryBuilder;

interface RoomRepositoryInterface {

    public function findOneById(int $id): ?Room;

    public function findOneByUuid(string $uuid): ?Room;

    public function persist(Room $room): void;

    public function remove(Room $room): void;
}
