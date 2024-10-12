<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\RoomCategory;
use App\Form\RoomCategoryType;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoomCategoriesController extends AbstractController {

    public function __construct(private RoomCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/categories', name: 'admin_roomcategories')]
    public function index(): Response {
        $roomCategories = $this->repository
            ->findAll();

        return $this->render('admin/rooms/categories/index.html.twig', [
            'categories' => $roomCategories
        ]);
    }

    #[Route(path: '/admin/rooms/categories/add', name: 'add_roomcategory')]
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

    #[Route(path: '/admin/rooms/categories/{uuid}/edit', name: 'edit_roomcategory')]
    public function edit(Request $request, RoomCategory $category) {
        $form = $this->createForm(RoomCategoryType::class, $category, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($category);

            $this->addFlash('success', 'rooms.categories.edit.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route(path: '/admin/rooms/categories/{uuid}/remove', name: 'remove_roomcategory')]
    public function remove(Request $request, RoomCategory $category, TranslatorInterface $translator) {
        if($category->getRooms()->count() > 0) {
            $this->addFlash('error', $translator->trans('rooms.categories.remove.error', [ '%name%' => $category->getName() ]));
            return $this->redirectToRoute('admin_roomcategories');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'rooms.categories.remove.confirm',
            'message_parameters' => [
                '%name%' => $category->getName()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($category);

            $this->addFlash('success', 'rooms.categories.remove.success');

            return $this->redirectToRoute('admin_roomcategories');
        }

        return $this->render('admin/rooms/categories/remove.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}