<?php

namespace App\Repository;

use App\Entity\WikiArticle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator;

class WikiArticleRepository implements WikiArticleRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
        $this->em->getConfiguration()->addCustomHydrationMode('tree', TreeObjectHydrator::class);
    }

    public function searchByQuery($query) {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('a')
            ->from(WikiArticle::class, 'a')
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
    public function findAll(): array {
        return $this->em
            ->getRepository(WikiArticle::class)
            ->createQueryBuilder('node')
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree');
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