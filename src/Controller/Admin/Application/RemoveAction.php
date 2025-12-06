<?php

namespace App\Controller\Admin\Application;

use App\Entity\Application;
use App\Repository\ApplicationRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    #[Route(path: '/admin/applications/{uuid}/remove', name: 'remove_application')]
    #[NotFoundRedirect(redirectRoute: 'applications', flashMessage: 'applications.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'applications', flashMessage: 'applications.not_found')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Application $application,
        ApplicationRepositoryInterface $repository,
        Request $request
    ): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'applications.remove.confirm',
            'message_parameters' => [
                '%name%' => $application->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->remove($application);
            $this->addFlash('success', 'applications.remove.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/remove.html.twig', [
            'application' => $application,
            'form' => $form->createView()
        ]);
    }
}