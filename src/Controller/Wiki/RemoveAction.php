<?php

namespace App\Controller\Wiki;

use App\Entity\WikiArticle;
use App\Repository\WikiArticleRepositoryInterface;
use App\Security\Voter\WikiVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    #[Route(path: '/wiki/{uuid}/remove', name: 'remove_wiki_article')]
    public function __invoke(
        Request $request,
        WikiArticleRepositoryInterface $repository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] WikiArticle $article
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(WikiVoter::REMOVE, $article);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'wiki.articles.remove.confirm',
            'message_parameters' => [
                '%article%' => $article->getName()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $repository->remove($article);

            $this->addFlash('success', 'wiki.articles.remove.success');
            return $this->redirectToRoute('wiki_article', [
                'uuid' => $article->getUuid()
            ]);
        }

        return $this->render('wiki/remove.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }
}