<?php

declare(strict_types=1);

namespace App\Wiki;

use App\Entity\WikiArticle;

class TreeHelper {

    /**
     * @param WikiArticle[] $root
     * @param bool $pathKey Whether to include the path as array key
     * @param WikiArticle|null $excludeChildren Excludes all children of the specified wiki article
     * @return WikiArticle[] Flat list of wiki articles
     */
    public function flattenTree(array $root, bool $pathKey = true, WikiArticle $excludeChildren = null): array {
        $result = [ ];

        foreach($root as $article) {
            $result += $this->internalFlattenTree($article, '');
        }

        if($pathKey === false) {
            $result = array_values($result);
        }

        return $result;
    }

    private function internalFlattenTree(WikiArticle $article, string $path, WikiArticle $excludeChildren = null): array {
        $result = [ ];
        $path = sprintf('%s / %s', $path, $article->getName());

        $result[$path] = $article;

        if(!$excludeChildren instanceof WikiArticle || $article->getId() !== $excludeChildren->getId()) {
            foreach ($article->getChildren() as $child) {
                $result += $this->internalFlattenTree($child, $path);
            }
        }

        return $result;
    }
}
