<?php

namespace App\Helper\Wiki;

use App\Entity\WikiArticle;

class WikiSearchResult {

    /** @var int */
    private $count;

    /** @var int */
    private $pages;

    /** @var int */
    private $page;

    /** @var WikiArticle[] */
    private $articles;

    /**
     * @param int $count
     * @param int $pages
     * @param int $page
     * @param WikiArticle[] $articles
     */
    public function __construct(int $count, int $pages, int $page, array $articles) {
        $this->count = $count;
        $this->pages = $pages;
        $this->page = $page;
        $this->articles = $articles;
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getPages(): int {
        return $this->pages;
    }

    /**
     * @return int
     */
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