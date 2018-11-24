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

    public function persist(NotificationSetting $notificationSetting) {
        $this->em->persist($notificationSetting);
        $this->em->flush();
    }

    public function remove(NotificationSetting $notificationSetting) {
        $this->em->remove($notificationSetting);
        $this->em->flush();
    }
}