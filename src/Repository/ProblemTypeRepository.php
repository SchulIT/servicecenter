<?php

namespace App\Repository;

use App\Entity\ProblemType;
use Doctrine\ORM\EntityManagerInterface;

class ProblemTypeRepository implements ProblemTypeRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(ProblemType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(ProblemType $problemType) {
        $this->em->persist($problemType);
        $this->em->flush();
    }

    public function remove(ProblemType $problemType) {
        $this->em->remove($problemType);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?ProblemType {
        return $this->em->getRepository(ProblemType::class)
            ->findOneBy([
                'id' => $id
            ]);
    }
}