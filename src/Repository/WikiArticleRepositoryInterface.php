<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\WikiArticle;

interface WikiArticleRepositoryInterface {
    /**
     * @return WikiArticle[]
     */
    public function searchByQuery(string $query): array;

    /**
     * @return WikiArticle[]
     */
    public function findAll(): array;

    public function persist(WikiArticle $article);

    public function remove(WikiArticle $article);
}
