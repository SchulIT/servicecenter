<?php

namespace App\Repository;

use App\Entity\AnnouncementCategory;
use Doctrine\ORM\EntityManagerInterface;

class AnnouncementCategoryRepository implements AnnouncementCategoryRepositoryInterface {

    private $_em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->_em = $entityManager;
    }

    public function findAllWithCurrentAnnouncements(\DateTime $today) {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['c', 'a'])
            ->from(AnnouncementCategory::class, 'c')
            ->leftJoin('c.announcements', 'a')
            ->where('a.startDate <= :today')
            ->andWhere(
                $qb->expr()->orX(
                    'a.endDate >= :today',
                    'a.endDate IS NULL'
                )
            )
            ->setParameter('today', $today);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->_em->getRepository(AnnouncementCategory::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }
}