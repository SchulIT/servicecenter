<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\ProblemType;
use Doctrine\ORM\EntityManagerInterface;

class ProblemTypeRepository implements ProblemTypeRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findAll(): array {
        return $this->em->getRepository(ProblemType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    #[Override]
    public function persist(ProblemType $problemType): void {
        $this->em->persist($problemType);
        $this->em->flush();
    }

    #[Override]
    public function remove(ProblemType $problemType): void {
        $this->em->remove($problemType);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function findOneById(int $id): ?ProblemType {
        return $this->em->getRepository(ProblemType::class)
            ->findOneBy([
                'id' => $id
            ]);
    }
}
