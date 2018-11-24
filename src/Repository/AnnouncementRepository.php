<?php

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class AnnouncementRepository implements AnnouncementRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function countActive(\DateTime $today) {
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

    public function findActiveByRoom(Room $room, \DateTime $today) {
        $qb = $this->em->createQueryBuilder();

        $qb->select('a')
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
    public function findOneById($id) {
        return $this->em->getRepository(Announcement::class)
            ->findOneBy(['id' => $id ]);
    }

    public function persist(Announcement $announcement) {
        $this->em->persist($announcement);
        $this->em->flush();
    }

    public function remove(Announcement $announcement) {
        $this->em->remove($announcement);
        $this->em->flush();
    }
}