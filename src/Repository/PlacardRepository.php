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

    public function persist(Placard $placard, array $oldDevices = [ ]) {
        foreach($oldDevices as $device) {
            if($placard->getDevices()->contains($device) !== true) {
                $this->em->remove($device);
            }
        }

        foreach($placard->getDevices() as $device) {
            $this->em->persist($device);
        }

        $this->em->persist($placard);
        $this->em->flush();
    }

    public function remove(Placard $placard) {
        $this->em->remove($placard);
        $this->em->flush();
    }
}