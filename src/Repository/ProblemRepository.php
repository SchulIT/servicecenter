<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemType;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Override;

class ProblemRepository implements ProblemRepositoryInterface {
    use ReturnTrait;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    private function getDefaultQueryBuilder(): QueryBuilder {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select(['p', 'pt', 'cb', 'a', 'd', 'r', 'dt', 'c', 'ccb'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.problemType', 'pt')
            ->leftJoin('p.createdBy', 'cb')
            ->leftJoin('p.assignee', 'a')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->leftJoin('d.type', 'dt')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.createdBy', 'ccb')
            ;

        return $qb;
    }

    /**
     * Filters the results by non-solved problems
     */
    private function filterClosedProblems(QueryBuilder $qb): QueryBuilder {
        $qb->andWhere('p.isOpen = true');

        return $qb;
    }

    #[Override]
    public function findOneById(int $id): ?Problem {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('p.id = :id')
            ->setParameter('id', $id);

        $this->filterClosedProblems($qb);

        $result = $qb->getQuery()->getResult();

        return $this->returnFirstOrNull($result);
    }

    #[Override]
    public function findByIds(array $ids): array {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->in('p.id', ':ids')
        )
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findByUuids(array $uuids): array {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->in('p.uuid', ':uuids')
        )
            ->setParameter('uuids', $uuids);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function findByUser(User $user, ?string $sortColumn = null, ?string $order = 'asc'): array {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cb.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function findByAssignee(User $user, string $sortColumn = null, string $order = 'asc'): array {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cp.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function countByUser(User $user): int {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(1)')
            ->from(Problem::class, 'p')
            ->leftJoin('p.createdBy', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $user->getId());

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    #[Override]
    public function countOpen(): int {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(1)')
            ->from(Problem::class, 'p');

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findAllPaginated(PaginationQuery $paginationQuery, ?Room $room = null, ?ProblemType $problemType = null, ?string $query = null, bool $onlyOpen = true): PaginatedResult {
        $qb = $this->getDefaultQueryBuilder()
            ->orderBy('p.updatedAt', 'DESC');

        if($onlyOpen) {
            $qb
                ->where('p.isOpen = :isOpen')
                ->setParameter('isOpen', true);
        }

        if($problemType instanceof ProblemType) {
            $qb->andWhere('pt.id = :problemType')
                ->setParameter('problemType', $problemType->getId());
        }

        if($room instanceof Room) {
            $qb->andWhere('r.id = :room')
                ->setParameter('room', $room->getId());
        }

        if(!empty($query)) {
            $qb->andWhere('p.content LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
    private function copyParameters(QueryBuilder $sourceBuilder, QueryBuilder $targetBuilder): void {
        /** @var Parameter[] $parameters */
        $parameters = $sourceBuilder->getParameters();
        foreach($parameters as $parameter) {
            $targetBuilder->setParameter($parameter->getName(), $parameter->getValue(), $parameter->getType());
        }
    }

    #[Override]
    public function persist(Problem $problem): void {
        $this->em->persist($problem);
        $this->em->flush();
    }

    #[Override]
    public function remove(Problem $problem): void {
        $this->em->remove($problem);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOpenByRoom(Room $room): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->where('r.id = :room')
            ->setParameter('room', $room->getId());

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOpenByDevice(Device $device): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->where('d.id = :device')
            ->setParameter('device', $device->getId());

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOpenByDeviceIds(array $deviceIds, ?int $type): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->where(
                $qb->expr()->in('d.id', ':devices')
            )
            ->andWhere('p.isOpen = true')
            ->setParameter('devices', $deviceIds);

        if($type !== null && $type > 0) {
            $qb
                ->andWhere('p.problemType = :type')
                ->setParameter('type', $type);
        }

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOpen(): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r');

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function findRelatedPaginated(PaginationQuery $paginationQuery, Problem $problem): PaginatedResult {
        $device = $problem->getDevice();

        $qb = $this->em->createQueryBuilder()
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->where('d.type = :type')
            ->andWhere('p.id != :problem')
            ->andWhere('d.room = :room')
            ->setParameter('type', $device->getType()->getId())
            ->setParameter('problem', $problem->getId())
            ->setParameter('room', $device->getRoom()->getId())
            ->orderBy('p.createdAt', 'DESC');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLatest(int $number, bool $includeMaintenance): array {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['p', 'd', 'r'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->orderBy('p.updatedAt', 'desc')
            ->setMaxResults($number);

        if($includeMaintenance === false) {
            $qb->andWhere('p.isMaintenance = false');
        }

        return $qb->getQuery()->getResult();
    }
}
