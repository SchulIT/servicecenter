<?php

namespace App\Controller\Application;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {

    #[Route(path: '/admin/applications/{uuid}/edit', name: 'edit_application')]
    #[NotFoundRedirect(redirectRoute: 'applications', flashMessage: 'applications.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'applications', flashMessage: 'applications.not_found')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application,
        ApplicationRepositoryInterface $repository,
        Request $request
    ): RedirectResponse|Response {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($application);
            $this->addFlash('success', 'applications.edit.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/edit.html.twig', [
            'form' => $form->createView(),
            'application' => $application
        ]);
    }

}