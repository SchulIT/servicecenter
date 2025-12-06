<?php

namespace App\Controller\Problem\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepositoryInterface;
use App\Security\Voter\CommentVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    #[Route(path: '/problems/{uuid}/comments/{commentUuid}/remove', name: 'remove_comment')]
    public function removeComment(
        Request $request,
        #[MapEntity(mapping: ['commentUuid' => 'uuid'])] Comment $comment,
        CommentRepositoryInterface $commentRepository
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(CommentVoter::REMOVE, $comment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problems.comments.remove.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $commentRepository->remove($comment);

            $this->addFlash('success', 'problems.comments.remove.success');
            return $this->redirectToRoute('show_problem', [
                'uuid' => $comment->getProblem()->getUuid()
            ]);
        }

        return $this->render('problems/comments/remove.html.twig', [
            'form' => $form->createView(),
            'problem' => $comment->getProblem()
        ]);
    }
}