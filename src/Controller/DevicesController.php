<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Form\DeviceType;
use App\Entity\Device;
use App\Helper\Devices\MultipleDeviceCreator;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ADMIN')")]
class DevicesController extends AbstractController {

    public function __construct(private DeviceRepositoryInterface $repository, private DeviceTypeRepositoryInterface $typeRepository)
    {
    }

    #[Route(path: '/devices', name: 'devices')]
    public function index(Request $request, RoomRepositoryInterface $roomRepository, RoomCategoryRepositoryInterface $roomCategoryRepository): Response {
        $q = $request->query->get('q', null);
        $room = $request->query->get('room') !== null ? $roomRepository->findOneByUuid($request->query->get('room')) : null;

        $types = $this->typeRepository
            ->findAllByQuery($q, $room);

        return $this->render('devices/index.html.twig', [
            'q' => $q,
            'types' => $types,
            'room' => $room,
            'categories' => $roomCategoryRepository->findAll()
        ]);
    }

    #[Route(path: '/devices/add', name: 'add_device')]
    public function add(Request $request, MultipleDeviceCreator $deviceCreator) {
        $form = $this->createForm(DeviceType::class, [ ], [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $devices = $deviceCreator->createDevices(
                $form->get('group_general')->get('room')->getData(),
                $form->get('group_general')->get('type')->getData(),
                $form->get('group_general')->get('name')->getData(),
                $form->get('group_general')->get('quantity')->getData(),
                $form->get('group_general')->get('start_index')->getData(),
                $form->get('group_general')->get('pad_length')->getData()
            );

            foreach($devices as $device) {
                $this->repository->persist($device);
            }

            $this->addFlash('success', 'devices.add.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/devices/{uuid}/edit', name: 'edit_device')]
    public function edit(Request $request, Device $device) {
        $form = $this->createForm(DeviceType::class, $device, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($device);

            $this->addFlash('success', 'devices.edit.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/edit.html.twig', [
            'form' => $form->createView(),
            'device' => $device
        ]);
    }

    #[Route(path: '/devices/{uuid}/remove', name: 'remove_device')]
    public function remove(Request $request, Device $device) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'devices.remove.confirm',
            'message_parameters' => [
                '%name%' => $device->getName(),
                '%count%' => $device->getProblems()->count()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($device);

            $this->addFlash('success', 'devices.remove.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/remove.html.twig', [
            'form' => $form->createView(),
            'device' => $device
        ]);
    }
}