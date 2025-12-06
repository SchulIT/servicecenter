<?php

namespace App\Controller\Wiki;

use App\Entity\WikiArticle;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowAction extends AbstractController {

    #[Route(path: '/wiki', name: 'wiki')]
    #[Route(path: '/wiki/{uuid}', name: 'wiki_article')]
    public function __invoke(
        WikiArticleRepositoryInterface $repository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ?WikiArticle $article = null
    ): Response {
        if($article instanceof WikiArticle) {
            $this->denyAccessUnlessGranted(WikiVoter::VIEW, $article);
        }

        /** @var WikiArticle[] $children */
        $children = $article instanceof WikiArticle ? $article->getChildren() : $repository->findAll();

        $childrenWithChildren = [ ];
        $childrenWithoutChildren = [ ];

        foreach($children as $child) {
            if($this->isGranted(WikiVoter::VIEW, $child)) {
                if ($child->getChildren()->count() > 0) {
                    $childrenWithChildren[] = $child;
                } else {
                    $childrenWithoutChildren[] = $child;
                }
            }
        }

        return $this->render('wiki/show.html.twig', [
            'article' => $article,
            'children' => $children,
            'childrenWithoutChildren' => $childrenWithoutChildren,
            'childrenWithChildren' => $childrenWithChildren
        ]);
    }
}