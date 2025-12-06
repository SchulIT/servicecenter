<?php

namespace App\Controller\Wiki;

use App\Entity\WikiArticle;
use App\Form\WikiArticleType;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    #[Route(path: '/wiki/articles/add', name: 'add_wiki_root_article')]
    #[Route(path: '/wiki/{uuid}/articles/add', name: 'add_wiki_article')]
    public function __invoke(
        WikiArticleRepositoryInterface $repository,
        Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ?WikiArticle $parent
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(WikiVoter::ADD, $parent);

        $article = (new WikiArticle())
            ->setParent($parent);
        $form = $this->createForm(WikiArticleType::class, $article, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($article);

            $this->addFlash('success', 'wiki.articles.add.success');

            if(!$parent instanceof WikiArticle) {
                return $this->redirectToRoute('wiki');
            }

            return $this->redirectToRoute('wiki_article', [
                'uuid' => $article->getUuid()
            ]);
        }

        return $this->render('wiki/add.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'parent' => $parent
        ]);
    }
}