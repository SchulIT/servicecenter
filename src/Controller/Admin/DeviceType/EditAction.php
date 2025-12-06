<?php

namespace App\Controller\Admin\DeviceType;

use App\Entity\DeviceType;
use App\Form\DeviceTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {

    public function __construct(private readonly DeviceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/devicetypes/{uuid}/edit', name: 'edit_devicetype')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] DeviceType $type): RedirectResponse|Response {
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
}