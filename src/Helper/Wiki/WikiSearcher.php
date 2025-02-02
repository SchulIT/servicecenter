<?php

namespace App\Helper\Wiki;

use App\Entity\WikiArticle;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class WikiSearcher {
    public function __construct(private WikiArticleRepositoryInterface $wikiArticleRepository, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function search(string $query, int $page, int $limit): WikiSearchResult {
        $articles = $this->wikiArticleRepository
            ->searchByQuery($query);

        /** @var WikiArticle[] $visibleArticles */
        $visibleArticles = [ ];

        foreach($articles as $article) {
            if($this->authorizationChecker->isGranted(WikiVoter::VIEW, $article)) {
                $visibleArticles[] = $article;
            }
        }

        $count = count($visibleArticles);
        $offset = ($page - 1) * $limit;
        $pages = $count > 0 ? ceil((float)$count / $count) : 0;

        if($offset > $count) {
            $offset = 0;
            $page = 1;
        }

        return new WikiSearchResult($count, $pages, $page, array_slice($visibleArticles, $offset, $limit));
    }
}