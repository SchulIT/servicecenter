<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\DeviceType;
use App\Form\DeviceTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeviceTypesController extends AbstractController {

    public function __construct(private readonly DeviceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/devicetypes', name: 'admin_devicetypes')]
    public function index(): Response {
        $types = $this->repository
            ->findAll();

        return $this->render('admin/devicetypes/index.html.twig', [
            'types' => $types
        ]);
    }

    #[Route(path: '/admin/devicetypes/add', name: 'add_devicetype')]
    public function add(Request $request): Response {
        $type = new DeviceType();

        $form = $this->createForm(DeviceTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'device_types.add.success');
            return $this->redirectToRoute('admin_devicetypes');
        }

        return $this->render('admin/devicetypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/admin/devicetypes/{uuid}/edit', name: 'edit_devicetype')]
    public function edit(Request $request, DeviceType $type): Response {
        $form = $this->createForm(DeviceTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'device_types.edit.success');
            return $this->redirectToRoute('admin_devicetypes');
        }

        return $this->render('admin/devicetypes/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }

    #[Route(path: '/admin/devicetypes/{uuid}/remove', name: 'remove_devicetype')]
    public function remove(Request $request, DeviceType $type, TranslatorInterface $translator): Response {
        if($type->getDevices()->count() > 0) {
            $this->addFlash('error',
                $translator->trans('device_types.remove.error.devices', [
                    '%type%' => $type->getName()
                ])
            );
            return $this->redirectToRoute('admin_devicetypes');
        }

        if($type->getProblemTypes()->count() > 0) {
            $this->addFlash('error',
                $translator->trans('device_types.remove.error.devices', [
                    '%type%' => $type->getName()
                ])
            );
            return $this->redirectToRoute('admin_devicetypes');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'device_types.remove.confirm',
            'message_parameters' => [
                '%name%' => $type->getName()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($type);

            $this->addFlash('success', 'device_types.remove.success');
            return $this->redirectToRoute('admin_devicetypes');
        }

        return $this->render('admin/devicetypes/add.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}