<?php

namespace App\Controller\Admin\RoomCategory;

use App\Entity\RoomCategory;
use App\Form\RoomCategoryType;
use App\Repository\RoomCategoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    public function __construct(private readonly RoomCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/categories/add', name: 'add_roomcategory')]
    public function add(Request $request): RedirectResponse|Response {
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
}