<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PaginationQuery;
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
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class ApplicationController extends AbstractController
{
    public function __construct(private readonly ApplicationRepositoryInterface $repository)
    {
    }
    #[Route(path: '/admin/applications', name: 'applications')]
    public function index(#[MapQueryParameter] int $page = 1): Response {
        return $this->render('admin/applications/index.html.twig', [
            'applications' => $this->repository->findAllPaginated(new PaginationQuery(page: $page))
        ]);
    }

    #[Route(path: '/admin/applications/add', name: 'add_application')]
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
    #[Route(path: '/admin/applications/{uuid}/edit', name: 'edit_application')]
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
    #[Route(path: '/admin/applications/{uuid}/remove', name: 'remove_application')]
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
