<?php

namespace App\Controller\Admin\Application;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepositoryInterface;
use App\Service\ApplicationKeyGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    #[Route(path: '/admin/applications/add', name: 'add_application')]
    public function __invoke(
        Request $request,
        ApplicationRepositoryInterface $repository,
        ApplicationKeyGenerator $keyGenerator
    ): RedirectResponse|Response {
        $application = (new Application());
        $application->setApiKey($keyGenerator->generateApiKey());

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($application);
            $this->addFlash('success', 'applications.add.success');

            return $this->redirectToRoute('applications');
        }

        return $this->render('admin/applications/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}