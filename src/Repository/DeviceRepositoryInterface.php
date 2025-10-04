<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;

interface DeviceRepositoryInterface {

    /**
     * @param int $id
     * @return Device|null
     */
    public function findOneById($id);

    public function persist(Device $device);

    public function remove(Device $device);
}
