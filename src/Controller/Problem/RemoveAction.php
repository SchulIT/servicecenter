<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    #[Route(path: '/problems/{uuid}/remove', name: 'remove_problem')]
    public function remove(
        Request $request,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(ProblemVoter::REMOVE, $problem);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problems.remove.confirm',
            'message_parameters' => [
                '%problem%' => sprintf("%s [%s]: %s", $problem->getDevice()->getRoom(), $problem->getDevice(), $problem->getProblemType())
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $problemRepository->remove($problem);
            $this->addFlash('success', 'problems.remove.success');

            return $this->redirectToRoute('problems');
        }

        return $this->render('problems/remove.html.twig', [
            'form' => $form->createView(),
            'problem' => $problem
        ]);
    }
}