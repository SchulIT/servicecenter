<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DeviceType;
use Override;
use App\Entity\ProblemType;
use Doctrine\ORM\EntityManagerInterface;

class ProblemTypeRepository implements ProblemTypeRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(ProblemType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    #[Override]
    public function persist(ProblemType $problemType): void {
        $this->em->persist($problemType);
        $this->em->flush();
    }

    #[Override]
    public function remove(ProblemType $problemType): void {
        $this->em->remove($problemType);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneById(int $id): ?ProblemType {
        return $this->em->getRepository(ProblemType::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    #[Override]
    public function findOneByUuid(string $uuid): ?ProblemType {
        return $this->em->getRepository(ProblemType::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    #[\Override]
    public function findAllPaginated(PaginationQuery $paginationQuery, DeviceType|null $deviceType = null): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select(['t'])
            ->from(ProblemType::class, 't')
            ->orderBy('t.name', 'asc');

        if($deviceType instanceof DeviceType) {
            $qb->andWhere($qb->expr()->eq('t.deviceType', ':deviceType'))
                ->setParameter('deviceType', $deviceType);
        }

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
}
