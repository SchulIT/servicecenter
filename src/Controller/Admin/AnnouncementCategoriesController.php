<?php

namespace App\Controller\Admin;

use App\Entity\AnnouncementCategory;
use App\Form\AnnouncementCategoryType;
use App\Repository\AnnouncementCategoryRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementCategoriesController extends AbstractController {

    public function __construct(private readonly AnnouncementCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/announcements/categories', name: 'admin_announcementcategories')]
    public function index(): Response {
        $categories = $this->repository
            ->findAll();

        return $this->render('admin/announcements/categories/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/admin/announcements/categories/add', name: 'add_announcementcategory')]
    public function add(Request $request): Response {
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

    #[Route(path: '/admin/announcements/categories/{uuid}/edit', name: 'edit_announcementcategory')]
    public function edit(Request $request, AnnouncementCategory $category): Response {
        $form = $this->createForm(AnnouncementCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'announcements.categories.edit.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route(path: '/admin/announcements/categories/{uuid}/remove', name: 'remove_announcementcategory')]
    public function remove(Request $request, AnnouncementCategory $category): Response {
        if($category->getAnnouncements()->count() > 0) {
            $this->addFlash('error', 'announcements.categories.remove.error');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'announcements.categories.remove.confirm',
            'message_parameters' => [
                '%name%' => $category->getName()
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'announcements.categories.remove.success');
            return $this->redirectToRoute('admin_announcementcategories');
        }

        return $this->render('admin/announcements/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}