<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RoomCategory;

interface RoomCategoryRepositoryInterface {

    /**
     * @return RoomCategory[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<RoomCategory>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult;

    public function persist(RoomCategory $category);

    public function remove(RoomCategory $category);
}
