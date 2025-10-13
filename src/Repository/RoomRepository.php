<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Override;

class RoomRepository implements RoomRepositoryInterface {

    use ReturnTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function persist(Room $room): void {
        $this->em->persist($room);
        $this->em->flush();
    }

    #[Override]
    public function remove(Room $room): void {
        $this->em->remove($room);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneById(int $id): ?Room {
        return $this->em->getRepository(Room::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneByUuid(string $uuid): ?Room {
        return $this->em->getRepository(Room::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    #[Override]
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(Room::class, 'r')
            ->orderBy('r.name', 'ASC');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
}
