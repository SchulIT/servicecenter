<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use DateTime;
use App\Entity\Announcement;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

readonly class AnnouncementRepository implements AnnouncementRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Override]
    public function countActive(DateTime $today): int {
        $qb = $this->em->createQueryBuilder();

        $qb->select('COUNT(1)')
            ->from(Announcement::class, 'a')
            ->where('a.startDate <= :today')
            ->andWhere(
                $qb->expr()->orX(
                    'a.endDate >= :today',
                    'a.endDate IS NULL'
                )
            )
            ->setParameter('today', $today);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findActive(DateTime $today): array {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['a', 'r'])
            ->from(Announcement::class, 'a')
            ->leftJoin('a.rooms', 'r')
            ->where('a.startDate <= :today')
            ->andWhere(
                $qb->expr()->orX(
                    'a.endDate >= :today',
                    'a.endDate IS NULL'
                )
            )
            ->setParameter('today', $today);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function findActiveByRoom(Room $room, DateTime $today): array {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['a', 'r'])
            ->from(Announcement::class, 'a')
            ->leftJoin('a.rooms', 'r')
            ->where('a.startDate <= :today')
            ->andWhere(
                $qb->expr()->orX(
                    'a.endDate >= :today',
                    'a.endDate IS NULL'
                )
            )
            ->andWhere('r.id = :room')
            ->setParameter('today', $today)
            ->setParameter('room', $room->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneById(int $id): ?Announcement {
        return $this->em->getRepository(Announcement::class)
            ->findOneBy(['id' => $id ]);
    }

    #[Override]
    public function persist(Announcement $announcement): void {
        $this->em->persist($announcement);
        $this->em->flush();
    }

    #[Override]
    public function remove(Announcement $announcement): void {
        $this->em->remove($announcement);
        $this->em->flush();
    }
}
