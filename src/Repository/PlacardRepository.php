<?php

namespace App\Repository;

use App\Entity\Placard;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class PlacardRepository implements PlacardRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findAll() {
        return $this->em->getRepository(Placard::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findOneByRoom(Room $room) {
        return $this->em->getRepository(Placard::class)
            ->findOneByRoom($room);
    }
}