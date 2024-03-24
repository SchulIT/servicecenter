<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;

class DeviceRepository implements DeviceRepositoryInterface {
    use ReturnTrait;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOneById($id) {
        return $this->em->getRepository(Device::class)
            ->findOneBy(['id' => $id ]);
    }

    public function persist(Device $device) {
        $this->em->persist($device);
        $this->em->flush();
    }

    public function remove(Device $device) {
        $this->em->remove($device);
        $this->em->flush();
    }
}