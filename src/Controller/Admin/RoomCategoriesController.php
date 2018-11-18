<?php

namespace App\Controller\Admin;

use App\Entity\RoomCategory;
use App\Form\RoomCategoryType;
use App\Repository\RoomCategoryRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RoomCategoriesController extends Controller {

    /**
     * @Route("/admin/rooms/categories", name="admin_roomcategories")
     */
    public function index(RoomCategoryRepositoryInterface $roomCategoryRepository) {
        $roomCategories = $roomCategoryRepository->findAll();

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
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(RoomCategoryType::class, $category, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();

        if($category->getRooms()->count() > 0) {
            $this->addFlash('error', $this->get('translator')->trans('rooms.categories.remove.error', [ '%name%' => $category->getName() ]));
            return $this->redirectToRoute('admin_roomcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('rooms.categories.remove.confirm', [ '%name%' => $category->getName() ])
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'rooms.categories.remove.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}