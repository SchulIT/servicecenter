<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface {
    /**
     * @return User[]
     */
    public function findAll(int|null $limit = null, int|null $offset = null): array;

    public function findOneByUsername(string $username): ?User;

    public function findOneById(int $id): ?User;

    public function persist(User $user);

    public function remove(User $user);
}
