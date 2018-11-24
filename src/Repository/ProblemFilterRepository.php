<?php

namespace App\Repository;

use App\Entity\ProblemFilter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ProblemFilterRepository implements ProblemFilterRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneByUser(User $user) {
        return $this->em->getRepository(ProblemFilter::class)
            ->findOneByUser($user);
    }

    public function persist(ProblemFilter $filter) {
        $this->em->persist($filter);
        $this->em->flush();
    }

    public function remove(ProblemFilter $filter) {
        $this->em->remove($filter);
        $this->em->flush();
    }

    public function removeFromUser(User $user) {
        $this->em
            ->createQueryBuilder()
            ->delete(ProblemFilter::class, 'f')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}