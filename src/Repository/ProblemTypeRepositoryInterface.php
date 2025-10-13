<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeviceType;
use App\Entity\ProblemType;

interface ProblemTypeRepositoryInterface {

    public function findOneById(int $id): ?ProblemType;

    public function findOneByUuid(string $uuid): ?ProblemType;

    /**
     * @return ProblemType[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @param DeviceType|null $deviceType
     * @return PaginatedResult<ProblemType>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery, DeviceType|null $deviceType = null): PaginatedResult;

    public function persist(ProblemType $problemType);

    public function remove(ProblemType $problemType);
}
