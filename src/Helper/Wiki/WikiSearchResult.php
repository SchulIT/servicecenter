<?php

namespace App\Helper\Wiki;

use App\Entity\WikiArticle;

readonly class WikiSearchResult {

    /**
     * @param WikiArticle[] $articles
     */
    public function __construct(private int $count, private int $pages, private int $page, private array $articles)
    {
    }

    public function getCount(): int {
        return $this->count;
    }

    public function getPages(): int {
        return $this->pages;
    }

    public function getPage(): int {
        return $this->page;
    }

    /**
     * @return WikiArticle[]
     */
    public function getArticles(): array {
        return $this->articles;
    }
}