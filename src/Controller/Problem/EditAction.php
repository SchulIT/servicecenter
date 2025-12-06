<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Form\ProblemType;
use App\Repository\ProblemRepositoryInterface;
use App\Security\Voter\ProblemVoter;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    #[Route(path: '/problems/{uuid}/edit', name: 'edit_problem')]
    public function __invoke(
        Request $request,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Problem $problem,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse|Response {
        $this->denyAccessUnlessGranted(ProblemVoter::EDIT, $problem);

        $form = $this->createForm(ProblemType::class, $problem);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $problemRepository->persist($problem);

            $this->addFlash('success', 'problems.edit.success');
            return $this->redirectToRoute('show_problem', [ 'uuid' => $problem->getUuid() ]);
        }

        return $this->render('problems/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}