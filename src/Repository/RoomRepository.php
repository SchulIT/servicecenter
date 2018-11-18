<?php

namespace App\Repository;

use App\Entity\Placard;
use App\Entity\Problem;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RoomRepository implements RoomRepositoryInterface {

    use ReturnTrait;

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function getQueryBuilderForRoomsWithoutPlacard() {
        $innerQb = $this->em->createQueryBuilder()
            ->select('room.id')
            ->from(Placard::class, 'p')
            ->leftJoin('p.room', 'room');

        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from(Room::class, 'r')
            ->where(
                $qb->expr()->notIn('r.id', $innerQb->getDQL())
            );

        return $qb;
    }

    /**
     * @inheritDoc
     */
    public function getRoomWithUnsolvedProblems(Room $room) {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['r', 'd', 'a', 'p'])
            ->from(Room::class, 'r')
            ->leftJoin('r.devices', 'd')
            ->leftJoin('d.problems', 'p')
            ->leftJoin('r.announcements', 'a')
            ->where(
                $qb->expr()->orX(
                    'p.status < :status',
                    'p.id IS NULL'
                )
            )
            ->andWhere('r.id = :room')
            ->setParameter('status', Problem::STATUS_SOLVED)
            ->setParameter('room', $room->getId());

        return $this->returnFirstOrNull($qb->getQuery()->getResult());
    }

    /**
     * @inheritDoc
     */
    public function getQueryBuilderForRoomsWithPlacard() {
        $innerQb = $this->em->createQueryBuilder()
            ->select('room.id')
            ->from(Placard::class, 'p')
            ->leftJoin('p.room', 'room');

        $qb = $this->em->createQueryBuilder();
        $qb->select('r')
            ->from(Room::class, 'r')
            ->where(
                $qb->expr()->in('r.id', $innerQb->getDQL())
            );

        return $qb;
    }
}