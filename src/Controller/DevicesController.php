<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\DeviceTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DevicesController extends Controller {
    /**
     * @Route("/devices", name="devices")
     */
    public function index(Request $request, DeviceTypeRepositoryInterface $deviceTypeRepository) {
        $q = $request->query->get('q', null);

        $types = $deviceTypeRepository
            ->findAllByQuery($q);

        return $this->render('devices/index.html.twig', [
            'q' => $q,
            'types' => $types
        ]);
    }

    /**
     * @Route("/devices/add", name="add_device")
     */
    public function add(Request $request) {
        $device = new Device();
        $form = $this->createForm(\App\Form\DeviceType::class, $device, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($device);
            $em->flush();

            $this->addFlash('success', 'devices.add.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/devices/{id}/edit", name="edit_device")
     */
    public function edit(Request $request, Device $device) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(\App\Form\DeviceType::class, $device, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($device);
            $em->flush();

            $this->addFlash('success', 'devices.edit.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/devices/{id}/remove", name="remove_device")
     */
    public function remove(Request $request, Device $device) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('devices.remove.confirm', ['%name%' => $device->getName(), '%count%' => $device->getProblems()->count()])
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($device);
            $em->flush();

            $this->addFlash('success', 'devices.remove.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}