<?php

declare(strict_types=1);

namespace App\Repository;

use DateTime;
use App\Entity\AnnouncementCategory;

interface AnnouncementCategoryRepositoryInterface {
    /**
     * @return AnnouncementCategory[]
     */
    public function findAllWithCurrentAnnouncements(DateTime $today): array;

    /**
     * @return AnnouncementCategory[]
     */
    public function findAll(): array;

    /**
     * @param PaginationQuery $paginationQuery
     * @return PaginatedResult<AnnouncementRepository>
     */
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult;

    public function persist(AnnouncementCategory $category): void;

    public function remove(AnnouncementCategory $category): void;
}
