<?php

namespace App\Controller\Admin\ProblemTypes;

use App\Entity\ProblemType;
use App\Form\ProblemTypeType;
use App\Repository\ProblemTypeRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    public function __construct(private readonly ProblemTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/problemtypes/{uuid}/edit', name: 'edit_problemtype')]
    #[NotFoundRedirect(redirectRoute: 'admin_problemtypes', flashMessage: 'problem_types.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'admin_problemtypes', flashMessage: 'problem_types.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ProblemType $type): RedirectResponse|Response {
        $form = $this->createForm(ProblemTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'problem_types.edit.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}