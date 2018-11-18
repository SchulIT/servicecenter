<?php

namespace App\Controller\Admin;

use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementCategoryType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AnnouncementCategoriesController extends Controller {

    /**
     * @Route("/admin/announcements/categories", name="admin_announcementcategories")
     */
    public function index(AnnouncementCategoryRepositoryInterface $announcementCategoryRepository) {
        $categories = $announcementCategoryRepository->findAll();

        return $this->render('admin/announcements/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/announcements/categories/add", name="add_announcementcategory")
     */
    public function add(Request $request) {
        $category = new AnnouncementCategory();
        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'announcements.categories.add.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/announcements/categories/{id}/edit", name="edit_announcementcategory")
     */
    public function edit(Request $request, AnnouncementCategory $category) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'announcements.categories.edit.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/announcements/categories/{id}/remove", name="remove_announcementcategory")
     */
    public function remove(Request $request, AnnouncementCategory $category) {
        $em = $this->getDoctrine()->getManager();

        if($category->getAnnouncements()->count() > 0) {
            $this->addFlash('error', 'Kategorie kann nicht gelöscht werden, da sie Ankündigungen enthält');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('announcements.categories.remove.confirm', [ '%name%' => $category->getTitle() ])
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'announcements.categories.remove.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/delete.html.twig', [
            'form' => $form->createView()
        ]);
    }
}