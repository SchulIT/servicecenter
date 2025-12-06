<?php

namespace App\Controller\Admin\Announcement;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    public function __construct(private readonly AnnouncementRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/{uuid}/edit', name: 'edit_announcement')]
    #[NotFoundRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): RedirectResponse|Response {
        $form = $this->createForm(AnnouncementType::class, $announcement, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($announcement);

            $this->addFlash('success', 'announcements.edit.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/edit.html.twig', [
            'form' => $form->createView(),
            'announcement' => $announcement
        ]);
    }
}