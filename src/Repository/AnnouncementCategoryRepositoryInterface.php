<?php

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

    public function persist(AnnouncementCategory $category): void;

    public function remove(AnnouncementCategory $category): void;
}