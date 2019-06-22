<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use App\Repository\AnnouncementRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnnouncementsController extends AbstractController {

    private $repository;

    public function __construct(AnnouncementRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/announcements", name="admin_announcements")
     */
    public function index(AnnouncementCategoryRepositoryInterface $categoryRepository) {
        $categories = $categoryRepository
            ->findAll();

        return $this->render('admin/announcements/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/announcements/add", name="add_announcement")
     */
    public function add(Request $request) {
        $announcement = new Announcement();

        $form = $this->createForm(AnnouncementType::class, $announcement, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $announcement->setCreatedBy($this->getUser());

            $this->repository->persist($announcement);

            $this->addFlash('success', 'announcements.add.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/announcements/{id}/edit", name="edit_announcement")
     */
    public function edit(Request $request, Announcement $announcement) {
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

    /**
     * @Route("/admin/announcements/{id}/remove", name="remove_announcement")
     */
    public function remove(Request $request, Announcement $announcement, TranslatorInterface $translator) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('announcements.remove.confirm', [ '%name%' => $announcement->getTitle() ])
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