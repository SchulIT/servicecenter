<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DeviceType;
use App\Entity\Room;

interface DeviceRepositoryInterface {

    public function findOneById(int $id): ?Device;

    public function findAllPaginated(PaginationQuery $paginationQuery, Room|null $room = null, DeviceType|null $deviceType = null, string|null $query = null): PaginatedResult;

    public function persist(Device $device);

    public function remove(Device $device);
}
