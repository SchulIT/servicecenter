<?php

namespace App\Repository;

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

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    private function getDefaultQueryBuilder() {
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
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function filterClosedProblems(QueryBuilder $qb) {
        $qb->andWhere('p.isOpen = true');

        return $qb;
    }

    public function findOneById($id) {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('p.id = :id')
            ->setParameter('id', $id);

        $this->filterClosedProblems($qb);

        $result = $qb->getQuery()->getResult();

        return $this->returnFirstOrNull($result);
    }

    public function findByIds(array $ids) {
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
    public function findByUuids(array $uuids) {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->in('p.uuid', ':uuids')
        )
            ->setParameter('uuids', $uuids);

        return $qb->getQuery()->getResult();
    }

    public function findByUser(User $user, $sortColumn = null, $order = 'asc') {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cb.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    public function findByAssignee(User $user, $sortColumn = null, $order = 'asc') {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cp.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    public function countByUser(User $user) {
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

    public function countOpen() {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(1)')
            ->from(Problem::class, 'p');

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function applyProblemFilter(QueryBuilder $qb, ProblemFilter $filter, $suffix = '') {
        if($filter->getRoom() !== null) {
            $qb
                ->andWhere(sprintf('r%s.id = :room', $suffix))
                ->setParameter('room', $filter->getRoom()->getId());
        }

        if($filter->getIncludeSolved() !== true) {
            $this->filterClosedProblems($qb);
        }

        if($filter->getIncludeMaintenance() !== true) {
            $qb
                ->andWhere(sprintf('p%s.isMaintenance = false', $suffix));
        }

        if($filter->getSortColumn() !== null && $filter->getSortOrder()) {
            $qb
                ->orderBy(sprintf('p%s.%s', $suffix, $filter->getSortColumn()), $filter->getSortOrder());
        }

        $qb->setMaxResults($filter->getNumItems());

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string|null $query
     * @return QueryBuilder
     */
    private function applyQuery(QueryBuilder $qb, $query = null, $suffix = '') {
        if(!empty($query)) {
            $qb
                ->andWhere(sprintf('p%s.content LIKE :query', $suffix))
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb;
    }

    public function getProblems(ProblemFilter $filter, $page = 1, $query = null) {
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

        switch($filter->getSortOrder()) {
            case 'asc':
                $order = 'asc';
                break;
        }

        $qb->addOrderBy($column, $order);

        return $qb->getQuery()->getResult();
    }

    public function countProblems(ProblemFilter $filter, $query = null) {
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

    private function copyParameters(QueryBuilder $sourceBuilder, QueryBuilder $targetBuilder) {
        /** @var Parameter[] $parameters */
        $parameters = $sourceBuilder->getParameters();
        foreach($parameters as $parameter) {
            $targetBuilder->setParameter($parameter->getName(), $parameter->getValue(), $parameter->getType());
        }
    }

    public function persist(Problem $problem) {
        $this->em->persist($problem);
        $this->em->flush();
    }

    public function remove(Problem $problem) {
        $this->em->remove($problem);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findOpenByRoom(Room $room) {
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
    public function findOpenByDevice(Device $device) {
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
    public function findOpen() {
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

        $this->filterClosedProblems($qb);

        return $qb->getQuery()->getResult();
    }
}