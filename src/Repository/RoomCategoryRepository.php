<?php

namespace App\Repository;

use App\Entity\Problem;
use App\Entity\RoomCategory;
use Doctrine\ORM\EntityManagerInterface;

class RoomCategoryRepository implements RoomCategoryRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findAll() {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'r'])
            ->from(RoomCategory::class, 'c')
            ->leftJoin('c.rooms', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('r.name', 'asc');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findWithOpenProblems() {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'r', 'd', 'a', 'p'])
            ->from(RoomCategory::class, 'c')
            ->leftJoin('c.rooms', 'r')
            ->leftJoin('r.devices', 'd')
            ->leftJoin('d.problems', 'p')
            ->leftJoin('r.announcements', 'a')
            ->where(
                $qb->expr()->orX(
                    'p.status < :status',
                    'p.id IS NULL'
                )
            )
            ->setParameter('status', Problem::STATUS_SOLVED);

        return $qb->getQuery()->getResult();
    }
}