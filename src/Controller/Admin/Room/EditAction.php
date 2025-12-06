<?php

namespace App\Controller\Admin\Room;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepositoryInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    public function __construct(private readonly RoomRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/{uuid}/edit', name: 'edit_room')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Room $room): RedirectResponse|Response {
        $form = $this->createForm(RoomType::class, $room, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($room);

            $this->addFlash('success', 'rooms.edit.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }
}