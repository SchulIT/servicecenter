<?php

namespace App\Repository;

use App\Entity\Placard;
use App\Entity\Problem;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class RoomRepository implements RoomRepositoryInterface {

    use ReturnTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function persist(Room $room): void {
        $this->em->persist($room);
        $this->em->flush();
    }

    public function remove(Room $room): void {
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