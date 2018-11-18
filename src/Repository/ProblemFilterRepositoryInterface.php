<?php

namespace App\Repository;

use App\Entity\ProblemFilter;
use App\Entity\User;

interface ProblemFilterRepositoryInterface {
    /**
     * @param User $user
     * @return ProblemFilter
     */
    public function findOneByUser(User $user);
}