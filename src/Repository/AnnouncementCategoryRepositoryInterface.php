<?php

namespace App\Repository;

use DateTime;
use App\Entity\AnnouncementCategory;

interface AnnouncementCategoryRepositoryInterface {
    /**
     * @return AnnouncementCategory[]
     */
    public function findAllWithCurrentAnnouncements(DateTime $today);

    /**
     * @return AnnouncementCategory[]
     */
    public function findAll();

    public function persist(AnnouncementCategory $category);

    public function remove(AnnouncementCategory $category);
}