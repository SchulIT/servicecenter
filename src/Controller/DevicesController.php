<?php

namespace App\Controller;

use App\Entity\Device;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\DeviceTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class DevicesController extends AbstractController {

    private $repository;
    private $typeRepository;

    public function __construct(DeviceRepositoryInterface $repository, DeviceTypeRepositoryInterface $typeRepository) {
        $this->repository = $repository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * @Route("/devices", name="devices")
     */
    public function index(Request $request) {
        $q = $request->query->get('q', null);

        $types = $this->typeRepository
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
            $this->repository->persist($device);

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
        $form = $this->createForm(\App\Form\DeviceType::class, $device, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($device);

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
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('devices.remove.confirm', ['%name%' => $device->getName(), '%count%' => $device->getProblems()->count()])
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($device);

            $this->addFlash('success', 'devices.remove.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}