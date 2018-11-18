<?php

namespace App\Repository;

use App\Entity\AnnouncementCategory;

interface AnnouncementCategoryRepositoryInterface {
    /**
     * @param \DateTime $today
     * @return AnnouncementCategory[]
     */
    public function findAllWithCurrentAnnouncements(\DateTime $today);

    /**
     * @return AnnouncementCategory[]
     */
    public function findAll();
}