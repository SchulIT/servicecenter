<?php

declare(strict_types=1);

namespace App\Repository;

use Override;
use App\Entity\WikiArticle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator;

class WikiArticleRepository implements WikiArticleRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em) {
        $this->em->getConfiguration()->addCustomHydrationMode('tree', TreeObjectHydrator::class);
    }

    #[Override]
    public function searchByQuery(string $query): array {
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
    #[Override]
    public function findAll(): array {
        return $this->em
            ->getRepository(WikiArticle::class)
            ->createQueryBuilder('node')
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree');
    }

    #[Override]
    public function persist(WikiArticle $article): void {
        $this->em->persist($article);
        $this->em->flush();
    }

    #[Override]
    public function remove(WikiArticle $article): void {
        $this->em->remove($article);
        $this->em->flush();
    }


}
