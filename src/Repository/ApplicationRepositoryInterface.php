<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Application;

interface ApplicationRepositoryInterface {
    public function findAll(): array;

    public function findOneByApiKey(string $key): ?Application;

    public function persist(Application $application): void;

    public function remove(Application $application): void;
}
