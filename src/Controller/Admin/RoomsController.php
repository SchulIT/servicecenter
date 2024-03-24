<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RoomsController extends AbstractController {

    public function __construct(private RoomRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms', name: 'admin_rooms')]
    public function index(RoomCategoryRepositoryInterface $categoryRepository): Response {
        $categories = $categoryRepository
            ->findAll();

        return $this->render('admin/rooms/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route(path: '/admin/rooms/add', name: 'add_room')]
    public function add(Request $request) {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($room);

            $this->addFlash('success', 'rooms.add.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/admin/rooms/{uuid}/edit', name: 'edit_room')]
    public function edit(Request $request, Room $room) {
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

    #[Route(path: '/admin/rooms/{uuid}/remove', name: 'remove_room')]
    public function remove(Request $request, Room $room, TranslatorInterface $translator) {
        if($room->getDevices()->count() > 0) {
            $this->addFlash('error',
                $translator->trans('rooms.remove.error', [ '%name%' => $room->getName() ])
            );
            return $this->redirectToRoute('admin_rooms');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'rooms.remove.confirm',
            'message_parameters' => [
                '%name%' => $room->getName()
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($room);

            $this->addFlash('success', 'rooms.remove.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }
}