<?php

namespace App\Repository;

use App\Entity\ProblemType;

interface ProblemTypeRepositoryInterface {

    /**
     * @return ProblemType[]
     */
    public function findAll();
}