<?php

namespace App\Repository;

use App\Entity\NotificationSetting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationSettingRepository implements NotificationSettingRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findOneByUser(User $user) {
        return $this->em->getRepository(NotificationSetting::class)
            ->findOneBy(['user' => $user]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(NotificationSetting::class)
            ->findAll();
    }
}