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
}