<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProblemFilter;
use App\Entity\User;

interface ProblemFilterRepositoryInterface {
    public function findOneByUser(User $user): ?ProblemFilter;

    public function persist(ProblemFilter $filter);

    public function remove(ProblemFilter $filter);

    public function removeFromUser(User $user);
}
