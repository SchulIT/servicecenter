<?php

namespace App\Controller\Admin\RoomCategory;

use App\Entity\RoomCategory;
use App\Form\RoomCategoryType;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {

    public function __construct(private readonly RoomCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/categories/{uuid}/edit', name: 'edit_roomcategory')]
    #[NotFoundRedirect(redirectRoute: 'admin_roomcategories', flashMessage: 'rooms.categories.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'admin_roomcategories', flashMessage: 'rooms.categories.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] RoomCategory $category): RedirectResponse|Response {
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
}