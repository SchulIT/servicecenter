<?php

namespace App\Repository;

use App\Entity\DeviceType;

interface DeviceTypeRepositoryInterface {

    /**
     * @param $query
     * @return DeviceType[]
     */
    public function findAllByQuery($query);

    /**
     * @return DeviceType[]
     */
    public function findAll();
}