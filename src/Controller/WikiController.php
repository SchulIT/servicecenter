<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\WikiArticle;
use App\Form\WikiArticleType;
use App\Helper\Wiki\WikiSearcher;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WikiController extends AbstractController {

    public const WIKI_SEARCH_LIMIT = 25;

    public function __construct(private WikiArticleRepositoryInterface $articleRepository)
    {
    }

    #[Route(path: '/wiki', name: 'wiki')]
    public function index() {
        return $this->showArticle(null);
    }

    #[Route(path: '/wiki/search', name: 'wiki_search')]
    public function search(Request $request, WikiSearcher $wikiSearcher) {
        $query = $request->query->get('q', null);

        if(empty($query)) {
            return $this->redirectToRoute('wiki');
        }

        $page = $request->query->getInt('page', 1);

        if(!is_numeric($page) || $page <= 0) {
            $page = 1;
        }

        $result = $wikiSearcher->search($query, $page, self::WIKI_SEARCH_LIMIT);

        return $this->render('wiki/search_results.html.twig', [
            'result' => $result,
            'q' => $query
        ]);
    }

    #[Route(path: '/wiki/articles/add', name: 'add_wiki_root_article')]
    #[Route(path: '/wiki/{uuid}/{slug}/articles/add', name: 'add_wiki_article')]
    public function addArticle(Request $request, ?WikiArticle $parent) {
        $this->denyAccessUnlessGranted(WikiVoter::ADD, $parent);

        $article = (new WikiArticle())
            ->setParent($parent);
        $form = $this->createForm(WikiArticleType::class, $article, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->persist($article);

            $this->addFlash('success', 'wiki.articles.add.success');

            if($parent === null) {
                return $this->redirectToRoute('wiki');
            }

            return $this->redirectToRoute('wiki_article', [
                'uuid' => $parent->getUuid(),
                'slug' => $parent->getSlug()
            ]);
        }

        return $this->render('wiki/add.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'parent' => $parent
        ]);
    }

    #[Route(path: '/wiki/{uuid}/{slug}/edit', name: 'edit_wiki_article')]
    public function editArticle(Request $request, WikiArticle $article) {
        $this->denyAccessUnlessGranted(WikiVoter::EDIT, $article);

        $form = $this->createForm(WikiArticleType::class, $article, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->persist($article);

            $this->addFlash('success', 'wiki.articles.edit.success');

            return $this->redirectToRoute('wiki_article', [
                'uuid' => $article->getUuid(),
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('wiki/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route(path: '/wiki/{uuid}/{slug}/remove', name: 'remove_wiki_article')]
    public function removeArticle(Request $request, WikiArticle $article) {
        $this->denyAccessUnlessGranted(WikiVoter::REMOVE, $article);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'wiki.articles.remove.confirm',
            'message_parameters' => [
                '%article%' => $article->getName()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->remove($article);

            $this->addFlash('success', 'wiki.articles.remove.success');
            return $this->redirectToRoute('wiki_article', [
                'uuid' => $article->getUuid(),
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('wiki/remove.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    #[Route(path: '/wiki/{uuid}/{slug}', name: 'wiki_article')]
    public function showArticle(?WikiArticle $article): Response {
        if($article !== null) {
            $this->denyAccessUnlessGranted(WikiVoter::VIEW, $article);
        }

        /** @var WikiArticle[] $children */
        $children = $article !== null ? $article->getChildren() : $this->articleRepository->findAll();

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