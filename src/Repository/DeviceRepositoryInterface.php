<?php

namespace App\Repository;

use App\Entity\Device;

interface DeviceRepositoryInterface {

    /**
     * @param int $id
     * @return Device|null
     */
    public function findOneById($id);

    /**
     * @param Device $device
     * @return Device
     */
    public function getDeviceWithUnresolvedProblems(Device $device);

    public function persist(Device $device);

    public function remove(Device $device);
}