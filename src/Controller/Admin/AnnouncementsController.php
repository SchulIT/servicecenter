<?php

namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AnnouncementsController extends Controller {
    /**
     * @Route("/admin/announcements", name="admin_announcements")
     */
    public function index(AnnouncementCategoryRepositoryInterface $announcementCategoryRepository) {
        $categories = $announcementCategoryRepository->findAll();

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

            $em = $this->getDoctrine()->getManager();
            $em->persist($announcement);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AnnouncementType::class, $announcement, [ ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($announcement);
            $em->flush();

            $this->addFlash('success', 'announcements.edit.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/announcements/{id}/remove", name="remove_announcement")
     */
    public function remove(Request $request, Announcement $announcement) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('announcements.remove.confirm', [ '%name%' => $announcement->getTitle() ])
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($announcement);
            $em->flush();

            $this->addFlash('success', 'announcements.edit.success');
            return $this->redirectToRoute('admin_announcements');
        }

        return $this->render('admin/announcements/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}