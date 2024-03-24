<?php

namespace App\Repository;

use App\Entity\ProblemFilter;
use App\Entity\User;

interface ProblemFilterRepositoryInterface {
    /**
     * @return ProblemFilter
     */
    public function findOneByUser(User $user);

    public function persist(ProblemFilter $filter);

    public function remove(ProblemFilter $filter);

    public function removeFromUser(User $user);
}