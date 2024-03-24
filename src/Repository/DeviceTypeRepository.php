<?php

namespace App\Repository;

use App\Entity\DeviceType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class DeviceTypeRepository implements DeviceTypeRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAllByQuery(?string $query, ?Room $room = null): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'd', 'r'])
            ->from(DeviceType::class, 'c')
            ->leftJoin('c.devices', 'd')
            ->leftJoin('d.room', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('d.name', 'asc');

        if(!empty($query)) {
            $qb->where('d.name LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        if($room !== null) {
            $qb->andWhere('r.id = :room')
                ->setParameter('room', $room->getId());
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(DeviceType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(DeviceType $deviceType) {
        $this->em->persist($deviceType);
        $this->em->flush();
    }

    public function remove(DeviceType $deviceType) {
        $this->em->remove($deviceType);
        $this->em->flush();
    }
}