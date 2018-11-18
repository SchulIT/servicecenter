<?php

namespace App\Repository;

use App\Entity\DeviceType;
use Doctrine\ORM\EntityManagerInterface;

class DeviceTypeRepository implements DeviceTypeRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findAllByQuery($query) {
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
}