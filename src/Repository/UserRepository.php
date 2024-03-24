<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {
    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy(['id' => $id]);
    }

    public function __construct(private EntityManagerInterface $em)
    {
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

    public function persist(User $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user) {
        $this->em->remove($user);
        $this->em->flush();
    }
}