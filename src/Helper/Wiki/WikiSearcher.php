<?php

declare(strict_types=1);

namespace App\Helper\Wiki;

use App\Entity\WikiArticle;
use App\Repository\PaginatedResult;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class WikiSearcher {
    public function __construct(
        private WikiArticleRepositoryInterface $wikiArticleRepository,
        private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @param string $query
     * @param int $page
     * @param int $limit
     * @return PaginatedResult<WikiArticle>
     */
    public function search(string $query, int $page, int $limit): PaginatedResult {
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

        if($offset > $count) {
            $offset = 0;
            $page = 1;
        }

        return new PaginatedResult(
            new ArrayCollection(array_slice($visibleArticles, $offset, $limit)),
            $count,
            $page,
            $limit
        );
    }
}
