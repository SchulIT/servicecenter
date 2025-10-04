<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\NotificationSetting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationSettingRepository implements NotificationSettingRepositoryInterface {
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findOneByUser(User $user): ?object {
        return $this->em->getRepository(NotificationSetting::class)
            ->findOneBy(['user' => $user]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(NotificationSetting::class)
            ->findAll();
    }

    #[Override]
    public function persist(NotificationSetting $notificationSetting): void {
        $this->em->persist($notificationSetting);
        $this->em->flush();
    }

    #[Override]
    public function remove(NotificationSetting $notificationSetting): void {
        $this->em->remove($notificationSetting);
        $this->em->flush();
    }
}
