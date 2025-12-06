<?php

namespace App\Controller\Problem;

use App\Entity\Device;
use App\Entity\Room;
use App\Repository\RoomRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class XhrListDevicesAction {
    #[Route(path: '/problems/xhr/devices', name: 'devices_ajax')]
    public function __invoke(
        Request $request,
        RoomRepositoryInterface $roomRepository,
        TranslatorInterface $translator
    ): JsonResponse {
        $roomId = $request->query->get('room', null);

        if($roomId === null) {
            return new JsonResponse([]);
        }

        $room = $roomRepository->findOneById(intval($roomId));

        if(!$room instanceof Room) {
            throw new NotFoundHttpException();
        }

        $devices = $room->getDevices();

        $result = [ ];

        $result[] = [
            'value' => '',
            'placeholder' => true,
            'label' => $translator->trans('label.choose.device')
        ];

        /** @var Device $device */
        foreach($devices as $device) {
            $result[] = [
                'value' => $device->getId(),
                'label' => sprintf('%s (%s)', $device->getName(), $device->getType()->getName()),
                'customProperties' => [
                    'type' => $device->getType()->getId()
                ]
            ];
        }

        return new JsonResponse($result);
    }
}