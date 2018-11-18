<?php

namespace App\Repository;

use App\Entity\NotificationSetting;
use App\Entity\User;

interface NotificationSettingRepositoryInterface {

    /**
     * @param User $user
     * @return NotificationSetting|null
     */
    public function findOneByUser(User $user);

    /**
     * @return NotificationSetting[]
     */
    public function findAll();
}