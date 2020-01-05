<?php

namespace App\Controller\Admin;

use App\Entity\DeviceType;
use App\Form\DeviceTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeviceTypesController extends AbstractController {

    private $repository;

    public function __construct(DeviceTypeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/devicetypes", name="admin_devicetypes")
     */
    public function index() {
        $types = $this->repository
            ->findAll();

        return $this->render('admin/devicetypes/index.html.twig', [
            'types' => $types
        ]);
    }

    /**
     * @Route("/admin/devicetypes/add", name="add_devicetype")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/admin/devicetypes/{id}/edit", name="edit_devicetype")
     */
    public function edit(Request $request, DeviceType $type) {
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

    /**
     * @Route("/admin/devicetypes/{id}/remove", name="remove_devicetype")
     */
    public function remove(Request $request, DeviceType $type, TranslatorInterface $translator) {
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