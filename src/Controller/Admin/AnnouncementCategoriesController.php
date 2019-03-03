<?php

namespace App\Controller\Admin;

use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementCategoryType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AnnouncementCategoriesController extends AbstractController {

    private $repository;

    public function __construct(AnnouncementCategoryRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/announcements/categories", name="admin_announcementcategories")
     */
    public function index() {
        $categories = $this->repository
            ->findAll();

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
            $this->repository->persist($category);

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
        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

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
    public function remove(Request $request, AnnouncementCategory $category, TranslatorInterface $translator) {
        if($category->getAnnouncements()->count() > 0) {
            $this->addFlash('error', 'announcements.categories.remove.error');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('announcements.categories.remove.confirm', [ '%name%' => $category->getName() ])
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'announcements.categories.remove.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}