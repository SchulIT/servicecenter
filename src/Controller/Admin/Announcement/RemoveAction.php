<?php

namespace App\Controller\Admin\Announcement;

use App\Entity\Announcement;
use App\Repository\AnnouncementRepositoryInterface;
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

    public function __construct(private readonly AnnouncementRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/{uuid}/remove', name: 'remove_announcement')]
    #[NotFoundRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'announcements.remove.confirm',
            'message_parameters' => [
                '%name%' => $announcement->getTitle()
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($announcement);

            $this->addFlash('success', 'announcements.edit.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/remove.html.twig', [
            'form' => $form->createView(),
            'announcement' => $announcement
        ]);
    }
}