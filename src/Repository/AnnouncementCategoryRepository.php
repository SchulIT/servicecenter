<?php

namespace App\Repository;

use DateTime;
use App\Entity\AnnouncementCategory;
use Doctrine\ORM\EntityManagerInterface;

readonly class AnnouncementCategoryRepository implements AnnouncementCategoryRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findAllWithCurrentAnnouncements(DateTime $today): array {
        $qb = $this->em->createQueryBuilder();

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
    public function findAll(): array {
        return $this->em->getRepository(AnnouncementCategory::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(AnnouncementCategory $category): void {
        $this->em->persist($category);
        $this->em->flush();
    }

    public function remove(AnnouncementCategory $category): void {
        $this->em->remove($category);
        $this->em->flush();
    }
}