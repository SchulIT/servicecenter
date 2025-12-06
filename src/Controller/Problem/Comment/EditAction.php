<?php

namespace App\Controller\Problem\Comment;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepositoryInterface;
use App\Security\Voter\CommentVoter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    #[Route(path: '/problems/{uuid}/comments/{commentUuid}/edit', name: 'edit_comment')]
    public function editComment(
        Request $request,
        #[MapEntity(mapping: ['commentUuid' => 'uuid'])] Comment $comment,
        CommentRepositoryInterface $commentRepository
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);

        $form = $this->createForm(CommentType::class, $comment, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $commentRepository->persist($comment);

            $this->addFlash('success', 'problems.comments.edit.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $comment->getProblem()->getUuid()
            ]);
        }

        return $this->render('problems/comments/edit.html.twig', [
            'form' => $form->createView(),
            'problem' => $comment->getProblem()
        ]);
    }
}