<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\ProblemFilter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProblemFilterRepository implements ProblemFilterRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneByUser(User $user) {
        return $this->em->getRepository(ProblemFilter::class)
            ->findOneByUser($user);
    }

    #[Override]
    public function persist(ProblemFilter $filter): void {
        $this->em->persist($filter);
        $this->em->flush();
    }

    #[Override]
    public function remove(ProblemFilter $filter): void {
        $this->em->remove($filter);
        $this->em->flush();
    }

    #[Override]
    public function removeFromUser(User $user): void {
        $this->em
            ->createQueryBuilder()
            ->delete(ProblemFilter::class, 'f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
