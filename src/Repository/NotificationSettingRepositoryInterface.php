<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NotificationSetting;
use App\Entity\User;

interface NotificationSettingRepositoryInterface {

    public function findOneByUser(User $user): ?NotificationSetting;

    /**
     * @return NotificationSetting[]
     */
    public function findAll(): array;

    public function persist(NotificationSetting $notificationSetting);

    public function remove(NotificationSetting $notificationSetting);
}
