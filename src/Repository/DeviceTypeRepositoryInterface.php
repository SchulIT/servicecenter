<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeviceType;
use App\Entity\Room;

interface DeviceTypeRepositoryInterface {

    public function findOneByUuid(string $uuid): ?DeviceType;

    /**
     * @return DeviceType[]
     */
    public function findAllByQuery(?string $query, ?Room $room = null): array;

    /**
     * @return DeviceType[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<DeviceType>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult;

    public function persist(DeviceType $deviceType);

    public function remove(DeviceType $deviceType);
}
