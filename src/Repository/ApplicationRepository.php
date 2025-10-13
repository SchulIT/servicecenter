<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

readonly class ApplicationRepository implements ApplicationRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(Application::class)
            ->findAll();
    }

    #[Override]
    public function findOneByApiKey(string $key): ?Application {
        return $this->em->getRepository(Application::class)
            ->findOneBy(['apiKey' => $key]);
    }

    #[Override]
    public function persist(Application $application): void {
        $this->em->persist($application);
        $this->em->flush();
    }

    #[Override]
    public function remove(Application $application): void {
        $this->em->remove($application);
        $this->em->flush();
    }

    #[Override]
    public function findAllPaginated(PaginationQuery $paginationQuery): PaginatedResult {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Application::class, 'a')
            ->orderBy('a.name', 'ASC');

        return PaginatedResult::fromQueryBuilder($qb, $paginationQuery);
    }
}
