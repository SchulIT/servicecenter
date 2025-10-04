<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;

interface DeviceRepositoryInterface {

    public function findOneById(int $id): ?Device;

    public function persist(Device $device);

    public function remove(Device $device);
}
