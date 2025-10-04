<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {
    #[Override]
    public function findOneById(int $id): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy(['id' => $id]);
    }

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findAll(int $limit = null, int $offset = null): array {
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

    #[Override]
    public function findOneByUsername(string $username): ?User {
        return $this->em->getRepository(User::class)
            ->findOneBy(['username' => $username]);
    }

    #[Override]
    public function persist(User $user): void {
        $this->em->persist($user);
        $this->em->flush();
    }

    #[Override]
    public function remove(User $user): void {
        $this->em->remove($user);
        $this->em->flush();
    }
}
