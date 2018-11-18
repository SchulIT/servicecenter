<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\ORM\EntityManagerInterface;

class DeviceRepository implements DeviceRepositoryInterface {
    use ReturnTrait;

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findOneById($id) {
        return $this->em->getRepository(Device::class)
            ->findOneBy(['id' => $id ]);
    }

    /**
     * @inheritDoc
     */
    public function getDeviceWithUnresolvedProblems(Device $device) {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(['d', 'p'])
            ->from(Device::class, 'd')
            ->leftJoin('d.problems', 'p')
            ->where(
                $qb->expr()->orX(
                    'p.status < :status',
                    'p.id IS NULL'
                )
            )
            ->andWhere('d.id = :device')
            ->setParameter('status', Problem::STATUS_SOLVED)
            ->setParameter('device', $device->getId());

        return $this->returnFirstOrNull($qb->getQuery()->getResult());
    }
}