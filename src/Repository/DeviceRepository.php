<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\Device;
use App\Entity\Problem;
use Doctrine\ORM\EntityManagerInterface;

class DeviceRepository implements DeviceRepositoryInterface {
    use ReturnTrait;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findOneById($id): ?object {
        return $this->em->getRepository(Device::class)
            ->findOneBy(['id' => $id ]);
    }

    #[Override]
    public function persist(Device $device): void {
        $this->em->persist($device);
        $this->em->flush();
    }

    #[Override]
    public function remove(Device $device): void {
        $this->em->remove($device);
        $this->em->flush();
    }
}
