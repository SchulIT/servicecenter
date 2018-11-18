<?php

namespace App\Repository;

use App\Entity\WikiCategory;
use Doctrine\ORM\EntityManagerInterface;

class WikiCategoryRepository implements WikiCategoryRepositoryInterface {
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findByParent(WikiCategory $category = null) {
        return $this->em->getRepository(WikiCategory::class)
            ->findBy(['parent' => $category]);
    }
}