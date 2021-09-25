<?php

namespace App\Repository;

use App\Entity\DeviceType;
use App\Entity\Room;

interface DeviceTypeRepositoryInterface {

    /**
     * @param string|null $query
     * @param Room|null $room
     * @return DeviceType[]
     */
    public function findAllByQuery(?string $query, ?Room $room = null): array;

    /**
     * @return DeviceType[]
     */
    public function findAll();

    public function persist(DeviceType $deviceType);

    public function remove(DeviceType $deviceType);
}