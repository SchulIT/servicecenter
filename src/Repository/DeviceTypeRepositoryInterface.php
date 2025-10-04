<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeviceType;
use App\Entity\Room;

interface DeviceTypeRepositoryInterface {

    /**
     * @return DeviceType[]
     */
    public function findAllByQuery(?string $query, ?Room $room = null): array;

    /**
     * @return DeviceType[]
     */
    public function findAll(): array;

    public function persist(DeviceType $deviceType);

    public function remove(DeviceType $deviceType);
}
