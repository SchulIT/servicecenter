<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\ProblemFilter;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

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

    private function applyProblemFilter(QueryBuilder $qb, ProblemFilter $filter, string $suffix = ''): QueryBuilder {
        if($filter->getRooms()->count() > 0) {
            $roomIds = $filter->getRooms()->map(fn(Room $room): ?int => $room->getId())->toArray();
            $qb
                ->andWhere(sprintf('r%s.id IN (:rooms)', $suffix))
                ->setParameter('rooms', $roomIds);
        }

        if(!$filter->getIncludeSolved()) {
            $this->filterClosedProblems($qb);
        }

        if(!$filter->getIncludeMaintenance()) {
            $qb
                ->andWhere(sprintf('p%s.isMaintenance = false', $suffix));
        }

        if($filter->getSortOrder() !== '' && $filter->getSortOrder() !== '0') {
            $qb
                ->orderBy(sprintf('p%s.%s', $suffix, $filter->getSortColumn()), $filter->getSortOrder());
        }

        $qb->setMaxResults($filter->getNumItems());

        return $qb;
    }

    private function applyQuery(QueryBuilder $qb, ?string $query = null, $suffix = ''): QueryBuilder {
        if($query !== null && $query !== '' && $query !== '0') {
            $qb
                ->andWhere(sprintf('p%s.content LIKE :query', $suffix))
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb;
    }

    #[Override]
    public function getProblems(ProblemFilter $filter, int $page = 1, string $query = null): array {
        $qb = $this->getDefaultQueryBuilder();

        $qbInner = $this->em->createQueryBuilder();
        $qbInner
            ->select('pInner.id')
            ->from(Problem::class, 'pInner')
            ->leftJoin('pInner.device', 'dInner')
            ->leftJoin('dInner.room', 'rInner');

        $this->applyProblemFilter($qbInner, $filter, 'Inner');
        $this->applyQuery($qbInner, $query);

        if($page > 1) {
            $offset = ($page - 1) * $filter->getNumItems();
            $qb->setFirstResult($offset);
        }

        $qb->setMaxResults($filter->getNumItems());

        $qb->where(
            $qb->expr()->in('p.id', $qbInner->getDQL())
        );

        $this->copyParameters($qbInner, $qb);

        // Apply sorting
        $column = 'p.updatedAt';
        $order = 'desc';

        switch($filter->getSortColumn()) {
            case 'createdAt':
                $column = 'p.createdAt';
                break;
            case 'priority':
                $column = 'p.priority';
                break;
            case 'room':
                $column = 'r.name';
                break;
        }

        if ($filter->getSortOrder() === 'asc') {
            $order = 'asc';
        }

        $qb->addOrderBy($column, $order);

        return $qb->getQuery()->getResult();
    }

    #[Override]
    public function countProblems(ProblemFilter $filter, string $query = null): int {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(DISTINCT p.id)')
            ->from(Problem::class, 'p')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r');

        $this->applyProblemFilter($qb, $filter);
        $this->applyQuery($qb, $query);

        return $qb->getQuery()->getSingleScalarResult();
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
