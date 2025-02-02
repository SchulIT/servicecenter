<?php

namespace App\Controller\Admin;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use App\Repository\AnnouncementRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementsController extends AbstractController {

    public function __construct(private readonly AnnouncementRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements', name: 'admin_announcements')]
    public function index(AnnouncementCategoryRepositoryInterface $categoryRepository): Response {
        $categories = $categoryRepository
            ->findAll();

        return $this->render('admin/announcements/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/admin/announcements/add', name: 'add_announcement')]
    public function add(Request $request): RedirectResponse|Response {
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

    #[Route(path: '/admin/announcements/{uuid}/edit', name: 'edit_announcement')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): RedirectResponse|Response {
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

    #[Route(path: '/admin/announcements/{uuid}/remove', name: 'remove_announcement')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): RedirectResponse|Response {
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