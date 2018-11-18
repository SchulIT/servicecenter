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
}