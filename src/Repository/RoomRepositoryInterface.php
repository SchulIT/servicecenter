<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Room;

interface RoomRepositoryInterface {

    public function findOneById(int $id): ?Room;

    public function findOneByUuid(string $uuid): ?Room;

    /**
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<Room>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult;

    public function persist(Room $room): void;

    public function remove(Room $room): void;
}
