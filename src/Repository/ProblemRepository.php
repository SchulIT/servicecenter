<?php

namespace App\Repository;

use App\Entity\Problem;
use App\Entity\ProblemFilter;
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
            ->select(['p', 'pt', 'cb', 'cp', 'd', 'r', 'dt', 'c', 'ccb'])
            ->from(Problem::class, 'p')
            ->leftJoin('p.problemType', 'pt')
            ->leftJoin('p.createdBy', 'cb')
            ->leftJoin('p.contactPerson', 'cp')
            ->leftJoin('p.device', 'd')
            ->leftJoin('d.room', 'r')
            ->leftJoin('d.type', 'dt')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.createdBy', 'ccb');

        return $qb;
    }

    /**
     * Filters the results by non-solved problems
     *
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function filterSolvedProblems(QueryBuilder $qb) {
        $qb->andWhere('p.status < :status')
            ->setParameter('status', Problem::STATUS_SOLVED);

        return $qb;
    }

    public function findOneById($id) {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('p.id = :id')
            ->setParameter('id', $id);

        $this->filterSolvedProblems($qb);

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

    public function findByUser(User $user, $sortColumn = null, $order = 'asc') {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cb.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterSolvedProblems($qb);

        return $qb->getQuery()->getResult();
    }

    public function findByContactPerson(User $user, $sortColumn = null, $order = 'asc') {
        $qb = $this->getDefaultQueryBuilder();

        $qb->where('cp.id = :id')
            ->setParameter('id', $user->getId());

        if($sortColumn !== null && $order !== null) {
            $qb->orderBy(sprintf('p.%s', $sortColumn), $order);
        }

        $this->filterSolvedProblems($qb);

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

        $this->filterSolvedProblems($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countOpen() {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('COUNT(1)')
            ->from(Problem::class, 'p');

        $this->filterSolvedProblems($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    private function applyProblemFilter(QueryBuilder $qb, ProblemFilter $filter, $suffix = '') {
        if($filter->getRoom() !== null) {
            $qb
                ->andWhere(sprintf('r%s.id = :room', $suffix))
                ->setParameter('room', $filter->getRoom()->getId());
        }

        if($filter->getIncludeSolved() !== true) {
            $this->filterSolvedProblems($qb);
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
            ->from(Problem::class, 'pInner');

        $this->applyProblemFilter($qbInner, $filter, 'Inner');
        $this->applyQuery($qbInner, $query);

        if($page > 1) {
            $offset = ($page - 1) * $filter->getNumItems();
            $qbInner->setFirstResult($offset);
        }

        $qb->where(
            $qb->expr()->in('p.id', $qbInner->getDQL())
        );

        $this->copyParameters($qbInner, $qb);

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
}