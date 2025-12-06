<?php

namespace App\Controller\Admin\ProblemTypes;

use App\Entity\ProblemType;
use App\Form\ProblemTypeType;
use App\Repository\ProblemTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {

    public function __construct(private readonly ProblemTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/problemtypes/add', name: 'add_problemtype')]
    public function __invoke(Request $request): RedirectResponse|Response {
        $type = new ProblemType();
        $form = $this->createForm(ProblemTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'problem_types.add.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}