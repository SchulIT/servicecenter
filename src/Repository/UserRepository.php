<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll($limit = null, $offset = null) {
        $qb = $this->em->createQueryBuilder();

        $qb->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username');

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findOneByUsername($username): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);
    }
}