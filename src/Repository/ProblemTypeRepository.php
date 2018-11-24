<?php

namespace App\Repository;

use App\Entity\ProblemType;
use Doctrine\ORM\EntityManagerInterface;

class ProblemTypeRepository implements ProblemTypeRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
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
}