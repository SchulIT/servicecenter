<?php

namespace App\Controller\Admin;

use App\Entity\Room;
use App\Entity\RoomCategory;
use App\Form\RoomType;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RoomsController extends Controller {

    /**
     * @Route("/admin/rooms", name="admin_rooms")
     */
    public function index(RoomCategoryRepositoryInterface $roomCategoryRepository) {
        $categories = $roomCategoryRepository->findAll();

        return $this->render('admin/rooms/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/rooms/add", name="add_room")
     */
    public function add(Request $request) {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room, [ ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'rooms.add.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/rooms/{alias}/edit", name="edit_room")
     */
    public function edit(Request $request, Room $room) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(RoomType::class, $room, [ ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($room);
            $em->flush();

            $this->addFlash('success', 'rooms.edit.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/rooms/{alias}/remove", name="remove_room")
     */
    public function remove(Request $request, Room $room) {
        $em = $this->getDoctrine()->getManager();

        if($room->getDevices()->count() > 0) {
            $this->addFlash('error',
                $this->get('translator')->trans('rooms.remove.error', [ '%name%' => $room->getName() ])
            );
            return $this->redirectToRoute('admin_rooms');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('rooms.remove.confirm', [ '%name%' => $room->getName() ])
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($room);
            $em->flush();

            $this->addFlash('success', 'rooms.remove.success');
            return $this->redirectToRoute('admin_rooms');
        }

        return $this->render('admin/rooms/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}