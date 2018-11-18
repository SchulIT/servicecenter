<?php

namespace App\Controller\Admin;

use App\Entity\DeviceType;
use App\Form\DeviceTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DeviceTypesController extends Controller {
    /**
     * @Route("/admin/devicetypes", name="admin_devicetypes")
     */
    public function index(DeviceTypeRepositoryInterface $deviceTypeRepository) {
        $types = $deviceTypeRepository->findAll();

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();

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
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(DeviceTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'device_types.edit.success');
            return $this->redirectToRoute('admin_devicetypes');
        }

        return $this->render('admin/devicetypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/devicetypes/{id}/remove", name="remove_devicetype")
     */
    public function remove(Request $request, DeviceType $type) {
        $em = $this->getDoctrine()->getManager();

        if($type->getDevices()->count() > 0) {
            $this->addFlash('error',
                sprintf('Der Geräte-Typ "%s" kann nicht gelöscht werden, da er noch Geräte beinhaltet', $type->getName())
            );
            return $this->redirectToRoute('admin_devicetypes');
        }

        if($type->getProblemTypes()->count() > 0) {
            $this->addFlash('error',
                sprintf('Der Geräte-Typ "%s" kann nicht gelöscht werden, da er noch Problem-Typen beinhaltet', $type->getName())
            );
            return $this->redirectToRoute('admin_devicetypes');
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('device_types.remove.confirm', [ '%name%' => $type->getName() ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($type);
            $em->flush();

            $this->addFlash('success', 'device_types.remove.success');
            return $this->redirectToRoute('admin_devicetypes');
        }

        return $this->render('admin/devicetypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}