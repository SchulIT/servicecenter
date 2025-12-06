<?php

namespace App\Controller\Admin\RoomCategory;

use App\Entity\RoomCategory;
use App\Repository\RoomCategoryRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveAction extends AbstractController {
    public function __construct(private readonly RoomCategoryRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/categories/{uuid}/remove', name: 'remove_roomcategory')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] RoomCategory $category, TranslatorInterface $translator): RedirectResponse|Response {
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