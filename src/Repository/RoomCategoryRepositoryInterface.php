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

    public function persist(RoomCategory $category);

    public function remove(RoomCategory $category);
}