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

    public function persist(Room $room) {
        $this->em->persist($room);
        $this->em->flush();
    }

    public function remove(Room $room) {
        $this->em->remove($room);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Room {
        return $this->em->getRepository(Room::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByUuid(string $uuid): ?Room {
        return $this->em->getRepository(Room::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }
}