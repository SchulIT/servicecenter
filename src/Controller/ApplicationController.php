<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepositoryInterface;
use App\Service\ApplicationKeyGenerator;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/applications')]
class ApplicationController extends AbstractController {

    public function __construct(private readonly ApplicationRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'applications')]
    public function index(): Response {
        $applications = $this->repository->findAll();

        return $this->render('admin/applications/index.html.twig', [
            'applications' => $applications
        ]);
    }

    #[Route(path: '/add', name: 'add_application')]
    public function add(Request $request, ApplicationKeyGenerator $keyGenerator): RedirectResponse|Response {
        $application = (new Application());
        $application->setApiKey($keyGenerator->generateApiKey());

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($application);
            $this->addFlash('success', 'applications.add.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_application')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application, Request $request): RedirectResponse|Response {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($application);
            $this->addFlash('success', 'applications.edit.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/edit.html.twig', [
            'form' => $form->createView(),
            'application' => $application
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_application')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application, Request $request): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'applications.remove.confirm',
            'message_parameters' => [
                '%name%' => $application->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($application);
            $this->addFlash('success', 'applications.remove.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/remove.html.twig', [
            'application' => $application,
            'form' => $form->createView()
        ]);
    }
}