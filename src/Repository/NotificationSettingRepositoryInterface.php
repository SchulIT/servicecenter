<?php

namespace App\Repository;

use App\Entity\NotificationSetting;
use App\Entity\User;

interface NotificationSettingRepositoryInterface {

    /**
     * @return NotificationSetting|null
     */
    public function findOneByUser(User $user);

    /**
     * @return NotificationSetting[]
     */
    public function findAll();

    public function persist(NotificationSetting $notificationSetting);

    public function remove(NotificationSetting $notificationSetting);
}