<?php

namespace App\Controller\Admin\Device;

use App\Form\DeviceType;
use App\Helper\Devices\MultipleDeviceCreator;
use App\Repository\DeviceRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    #[Route(path: '/admin/devices/add', name: 'add_device')]
    public function add(
        Request $request,
        MultipleDeviceCreator $deviceCreator,
        DeviceRepositoryInterface $repository
    ): RedirectResponse|Response {
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
                $repository->persist($device);
            }

            $this->addFlash('success', 'devices.add.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}