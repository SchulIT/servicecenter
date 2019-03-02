<?php

namespace App\Controller\Admin;

use App\Entity\RoomCategory;
use App\Form\RoomCategoryType;
use App\Repository\RoomCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomCategoriesController extends AbstractController {

    private $repository;

    public function __construct(RoomCategoryRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/rooms/categories", name="admin_roomcategories")
     */
    public function index() {
        $roomCategories = $this->repository
            ->findAll();

        return $this->render('admin/rooms/categories/index.html.twig', [
            'categories' => $roomCategories
        ]);
    }

    /**
     * @Route("/admin/rooms/categories/add", name="add_roomcategory")
     */
    public function add(Request $request) {
        $category = new RoomCategory();

        $form = $this->createForm(RoomCategoryType::class, $category, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'rooms.categories.add.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/rooms/categories/{id}/edit", name="edit_roomcategory")
     */
    public function edit(Request $request, RoomCategory $category) {
        $form = $this->createForm(RoomCategoryType::class, $category, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'rooms.categories.edit.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/rooms/categories/{id}/remove", name="remove_roomcategory")
     */
    public function remove(Request $request, RoomCategory $category) {
        if($category->getRooms()->count() > 0) {
            $this->addFlash('error', $this->get('translator')->trans('rooms.categories.remove.error', [ '%name%' => $category->getName() ]));
            return $this->redirectToRoute('admin_roomcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('rooms.categories.remove.confirm', [ '%name%' => $category->getName() ])
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'rooms.categories.remove.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}