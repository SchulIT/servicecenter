<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProblemType;

interface ProblemTypeRepositoryInterface {

    public function findOneById(int $id): ?ProblemType;

    /**
     * @return ProblemType[]
     */
    public function findAll(): array;

    public function persist(ProblemType $problemType);

    public function remove(ProblemType $problemType);
}
