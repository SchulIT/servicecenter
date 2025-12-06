<?php

namespace App\Controller\Admin\Room;

use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveAction extends AbstractController {
    public function __construct(private readonly RoomRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/rooms/{uuid}/remove', name: 'remove_room')]
    #[NotFoundRedirect(redirectRoute: 'admin_rooms', flashMessage: 'rooms.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'admin_rooms', flashMessage: 'rooms.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Room $room, TranslatorInterface $translator): RedirectResponse|Response {
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