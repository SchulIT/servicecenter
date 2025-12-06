<?php

namespace App\Controller\Admin\ProblemTypes;

use App\Entity\ProblemType;
use App\Repository\ProblemTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    public function __construct(private readonly ProblemTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/problemtypes/{uuid}/remove', name: 'remove_problemtype')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ProblemType $type): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problem_types.remove.confirm',
            'message_parameters' => [
                '%name%' => $type->getName(),
                '%num%' => $type->getProblems()->count()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($type);

            $this->addFlash('success', 'problem_types.remove.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}