<?php

namespace App\Controller\Admin\Announcement;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    public function __construct(private readonly AnnouncementRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/add', name: 'add_announcement')]
    public function __invoke(Request $request): RedirectResponse|Response {
        $announcement = new Announcement();

        $form = $this->createForm(AnnouncementType::class, $announcement, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($announcement);

            $this->addFlash('success', 'announcements.add.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}