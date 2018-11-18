<?php

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
     * @return User|null
     */
    public function findOneByUsername($username): ?User;
}