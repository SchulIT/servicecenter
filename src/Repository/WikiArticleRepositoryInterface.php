<?php

namespace App\Repository;

use App\Entity\WikiArticle;

interface WikiArticleRepositoryInterface {
    /**
     * @param string $query
     * @return WikiArticle[]
     */
    public function searchByQuery($query);

    /**
     * @return WikiArticle[]
     */
    public function findAll(): array;

    public function persist(WikiArticle $article);

    public function remove(WikiArticle $article);
}