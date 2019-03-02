<?php

namespace App\Repository;

use App\Entity\WikiArticle;
use App\Entity\WikiCategory;
use Doctrine\ORM\EntityManagerInterface;

class WikiArticleRepository implements WikiArticleRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function searchByQuery($query) {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select(['a', 'c'])
            ->from(WikiArticle::class, 'a')
            ->leftJoin('a.category', 'c')
            ->where(
                $qb->expr()->orX(
                    'MATCH(a.name) AGAINST (:query) > 0',
                    'MATCH(a.content) AGAINST (:query) > 0',
                    $qb->expr()->like('a.name', ':likeQuery')
                )
            )
            ->setParameter('query', $query)
            ->setParameter('likeQuery', '%' . $query . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByCategory(?WikiCategory $category) {
        return $this->em->getRepository(WikiArticle::class)
            ->findBy(['category' => $category]);
    }

    public function persist(WikiArticle $article) {
        $this->em->persist($article);
        $this->em->flush();
    }

    public function remove(WikiArticle $article) {
        $this->em->remove($article);
        $this->em->flush();
    }
}