<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PaginationQuery;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Form\DeviceType;
use App\Entity\Device;
use App\Helper\Devices\MultipleDeviceCreator;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\RoomCategoryRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DevicesController extends AbstractController {

    public function __construct(private readonly DeviceRepositoryInterface $repository, private readonly DeviceTypeRepositoryInterface $typeRepository)
    {
    }

    #[Route(path: '/devices', name: 'devices')]
    public function index(
        RoomRepositoryInterface $roomRepository,
        DeviceTypeRepositoryInterface $deviceTypeRepository,
        RoomCategoryRepositoryInterface $roomCategoryRepository,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(name: 'dt')] string|null $deviceTypeUuid = null,
        #[MapQueryParameter(name: 'room')] string|null $roomUuid = null,
        #[MapQueryParameter] string|null $q = null
    ): Response {
        $room = null;
        $deviceType = null;

        if(!empty($deviceTypeUuid)) {
            $deviceType = $deviceTypeRepository->findOneByUuid($deviceTypeUuid);
        }

        if(!empty($roomUuid)) {
            $room = $roomRepository->findOneByUuid($roomUuid);
        }

        return $this->render('devices/index.html.twig', [
            'q' => $q,
            'room' => $room,
            'deviceType' => $deviceType,
            'deviceTypes' => $deviceTypeRepository->findAll(),
            'categories' => $roomCategoryRepository->findAll(),
            'devices' => $this->repository->findAllPaginated(
                new PaginationQuery(page: $page),
                room: $room,
                deviceType: $deviceType,
                query: $q
             )
        ]);
    }

    #[Route(path: '/devices/add', name: 'add_device')]
    public function add(Request $request, MultipleDeviceCreator $deviceCreator): RedirectResponse|Response {
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
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Device $device): RedirectResponse|Response {
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
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] Device $device): RedirectResponse|Response {
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
