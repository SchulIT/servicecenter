<?php

namespace App\Repository;

use App\Entity\RoomCategory;

interface RoomCategoryRepositoryInterface {

    /**
     * @return RoomCategory[]
     */
    public function findAll();

    /**
     * @return RoomCategory[]
     */
    public function findWithOpenProblems();
}