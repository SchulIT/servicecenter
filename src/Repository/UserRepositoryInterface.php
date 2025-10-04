<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface {
    /**
     * @param null|int $limit
     * @param null|int $offset
     * @return User[]
     */
    public function findAll($limit = null, $offset = null);

    /**
     * @param string $username
     */
    public function findOneByUsername($username): ?User;

    public function findOneById(int $id): ?User;

    public function persist(User $user);

    public function remove(User $user);
}
