<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\Problem;
use App\Entity\RoomCategory;
use Doctrine\ORM\EntityManagerInterface;

class RoomCategoryRepository implements RoomCategoryRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findAll(): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['c', 'r'])
            ->from(RoomCategory::class, 'c')
            ->leftJoin('c.rooms', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('r.name', 'asc');

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function persist(RoomCategory $category): void {
        $this->em->persist($category);
        $this->em->flush();
    }

    #[Override]
    public function remove(RoomCategory $category): void {
        $this->em->remove($category);
        $this->em->flush();
    }

    #[\Override]
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select(['c', 'r'])
            ->from(RoomCategory::class, 'c')
            ->leftJoin('c.rooms', 'r')
            ->orderBy('c.name', 'asc')
            ->addOrderBy('r.name', 'asc');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
}
