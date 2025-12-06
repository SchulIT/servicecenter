<?php

namespace App\Controller\Admin\Device;

use App\Entity\Device;
use App\Form\DeviceType;
use App\Repository\DeviceRepositoryInterface;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditAction extends AbstractController {
    #[Route(path: '/admin/devices/{uuid}/edit', name: 'edit_device')]
    #[NotFoundRedirect(redirectRoute: 'devices', flashMessage: 'devices.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'devices', flashMessage: 'devices.not_found')]
    public function __invoke(
        Request $request,
        DeviceRepositoryInterface $repository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Device $device
    ): RedirectResponse|Response {
        $form = $this->createForm(DeviceType::class, $device, [ ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $repository->persist($device);

            $this->addFlash('success', 'devices.edit.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/edit.html.twig', [
            'form' => $form->createView(),
            'device' => $device
        ]);
    }
}