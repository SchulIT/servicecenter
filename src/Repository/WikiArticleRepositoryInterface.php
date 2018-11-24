<?php

namespace App\Repository;

use App\Entity\WikiArticle;
use App\Entity\WikiCategory;

interface WikiArticleRepositoryInterface {
    /**
     * @param string $query
     * @return WikiArticle[]
     */
    public function searchByQuery($query);

    /**
     * @param WikiCategory $category
     * @return WikiArticle[]
     */
    public function findByCategory(WikiCategory $category);

    public function persist(WikiArticle $article);

    public function remove(WikiArticle $article);
}