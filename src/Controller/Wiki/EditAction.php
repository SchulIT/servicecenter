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

class EditAction extends AbstractController {
    #[Route(path: '/wiki/{uuid}/edit', name: 'edit_wiki_article')]
    public function __invoke(
        Request $request,
        WikiArticleRepositoryInterface $repository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] WikiArticle $article
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(WikiVoter::EDIT, $article);

        $form = $this->createForm(WikiArticleType::class, $article, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($article);

            $this->addFlash('success', 'wiki.articles.edit.success');

            return $this->redirectToRoute('wiki_article', [
                'uuid' => $article->getUuid()
            ]);
        }

        return $this->render('wiki/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }
}