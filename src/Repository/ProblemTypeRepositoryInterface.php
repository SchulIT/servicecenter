<?php

namespace App\Repository;

use App\Entity\ProblemType;

interface ProblemTypeRepositoryInterface {

    /**
     * @return ProblemType|null
     */
    public function findOneById(int $id): ?ProblemType;

    /**
     * @return ProblemType[]
     */
    public function findAll();

    public function persist(ProblemType $problemType);

    public function remove(ProblemType $problemType);
}