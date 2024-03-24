<?php

namespace App\Repository;

use App\Entity\Problem;
use App\Entity\RoomCategory;
use Doctrine\ORM\EntityManagerInterface;

class RoomCategoryRepository implements RoomCategoryRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAll() {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'r'])
            ->from(RoomCategory::class, 'c')
            ->leftJoin('c.rooms', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('r.name', 'asc');

        return $qb->getQuery()->getResult();
    }

    public function persist(RoomCategory $category) {
        $this->em->persist($category);
        $this->em->flush();
    }

    public function remove(RoomCategory $category) {
        $this->em->remove($category);
        $this->em->flush();
    }
}