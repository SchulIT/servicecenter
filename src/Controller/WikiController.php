<?php

namespace App\Controller;

use App\Entity\WikiArticle;
use App\Entity\WikiCategory;
use App\Form\WikiArticleType;
use App\Form\WikiCategoryType;
use App\Helper\Wiki\WikiSearcher;
use App\Repository\WikiArticleRepositoryInterface;
use App\Repository\WikiCategoryRepositoryInterface;
use App\Security\Voter\WikiVoter;
use EasySlugger\SluggerInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WikiController extends AbstractController {

    const WIKI_SEARCH_LIMIT = 25;

    private $articleRepository;
    private $categoryRepository;

    public function __construct(WikiArticleRepositoryInterface $wikiArticleRepository, WikiCategoryRepositoryInterface $wikiCategoryRepository) {
        $this->articleRepository = $wikiArticleRepository;
        $this->categoryRepository = $wikiCategoryRepository;
    }

    /**
     * @Route("/wiki", name="wiki")
     * @Route("/wiki/{uuid}/{slug}", name="wiki_category")
     */
    public function showCategory(?WikiCategory $category) {
        $isRootCategory = false;

        if($category === null) {
            $category = (new WikiCategory());

            $categories = $this->categoryRepository
                ->findByParent(null);

            foreach($categories as $c) {
                $category->addCategory($c);
            }

            $articles = $this->articleRepository
                ->findByCategory(null);

            foreach($articles as $a) {
                $category->addArticle($a);
            }

            $isRootCategory = true;
        }

        $this->denyAccessUnlessGranted(WikiVoter::VIEW, $category);

        return $this->render('wiki/category.html.twig', [
            'category' => $category,
            'isRootCategory' => $isRootCategory
        ]);
    }

    /**
     * @Route("/wiki/a/{uuid}/{slug}", name="wiki_article")
     */
    public function showArticle(WikiArticle $article) {
        $this->denyAccessUnlessGranted(WikiVoter::VIEW, $article);

        return $this->render('wiki/article.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/wiki/search", name="wiki_search")
     */
    public function search(Request $request, WikiSearcher $wikiSearcher) {
        $query = $request->query->get('q', null);

        if(empty($query)) {
            return $this->redirectToRoute('wiki');
        }

        $page = $request->query->get('page', 1);

        if(!is_numeric($page) || $page <= 0) {
            $page = 1;
        }

        $result = $wikiSearcher->search($query, $page, static::WIKI_SEARCH_LIMIT);

        return $this->render('wiki/search_results.html.twig', [
            'result' => $result,
            'q' => $query
        ]);
    }

    /**
     * @Route("/wiki/articles/add", name="add_wiki_root_article")
     * @Route("/wiki/{uuid}/{slug}/articles/add", name="add_wiki_article")
     */
    public function addArticle(Request $request, WikiCategory $parentCategory = null) {
        $this->denyAccessUnlessGranted(WikiVoter::ADD, $parentCategory);

        $article = (new WikiArticle())
            ->setCategory($parentCategory);
        $form = $this->createForm(WikiArticleType::class, $article, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->articleRepository->persist($article);

            $this->addFlash('success', 'wiki.articles.add.success');

            if($parentCategory === null) {
                return $this->redirectToRoute('wiki');
            }

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $parentCategory->getUuid(),
                'slug' => $parentCategory->getSlug()
            ]);
        }

        return $this->render('wiki/articles/add.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'parent' => $parentCategory
        ]);
    }

    /**
     * @Route("/wiki/a/{uuid}/{slug}/edit", name="edit_wiki_article")
     */
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

        return $this->render('wiki/articles/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/wiki/a/{uuid}/{slug}/remove", name="remove_wiki_article")
     */
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

        return $this->render('wiki/articles/remove.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/wiki/categories/add", name="add_wiki_root_category")
     * @Route("/wiki/{uuid}/{slug}/categories/add", name="add_wiki_category")
     */
    public function addCategory(Request $request, WikiCategory $parentCategory = null) {
        $this->denyAccessUnlessGranted(WikiVoter::ADD, $parentCategory);

        $category = (new WikiCategory())
            ->setParent($parentCategory);

        $form = $this->createForm(WikiCategoryType::class, $category, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->persist($category);

            $this->addFlash('success', 'wiki.categories.add.success');

            if($parentCategory === null) {
                return $this->redirectToRoute('wiki');
            }

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $parentCategory->getUuid(),
                'slug' => $parentCategory->getSlug()
            ]);
        }

        return $this->render('wiki/categories/add.html.twig', [
            'parent' => $parentCategory,
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/wiki/{uuid}/{slug}/edit", name="edit_wiki_category")
     */
    public function editCategory(Request $request, WikiCategory $category) {
        $this->denyAccessUnlessGranted(WikiVoter::EDIT, $category);

        $form = $this->createForm(WikiCategoryType::class, $category, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->persist($category);

            $this->addFlash('success', 'wiki.categories.edit.success');

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $category->getUuid(),
                'slug' => $category->getSlug()
            ]);
        }

        return $this->render('wiki/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/wiki/{uuid}/{slug}/remove", name="remove_wiki_category")
     */
    public function removeCategory(Request $request, WikiCategory $category) {
        $this->denyAccessUnlessGranted(WikiVoter::REMOVE, $category);

        if($category->getArticles()->count() > 0) {
            $this->addFlash('error', 'wiki.categories.remove.error.articles');

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $category->getUuid(),
                'slug' => $category->getSlug()
            ]);
        }

        if($category->getCategories()->count() > 0) {
            $this->addFlash('error', 'wiki.categories.remove.error.categories');

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $category->getUuid(),
                'slug' => $category->getSlug()
            ]);
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'wiki.categories.remove.confirm',
            'message_parameters' => [
                '%name%' => $category->getName()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->remove($category);

            $this->addFlash('success', 'wiki.categories.remove.success');

            return $this->redirectToRoute('wiki_category', [
                'uuid' => $category->getUuid(),
                'slug' => $category->getSlug()
            ]);
        }

        return $this->render('wiki/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}