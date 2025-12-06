<?php

namespace App\Controller\Admin\Device;

use App\Entity\Device;
use App\Repository\DeviceRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RemoveAction extends AbstractController {
    #[Route(path: '/admin/devices/{uuid}/remove', name: 'remove_device')]
    #[NotFoundRedirect(redirectRoute: 'devices', flashMessage: 'devices.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'devices', flashMessage: 'devices.not_found')]
    public function __invoke(
        Request $request,
        DeviceRepositoryInterface $repository,
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Device $device
    ): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'devices.remove.confirm',
            'message_parameters' => [
                '%name%' => $device->getName(),
                '%count%' => $device->getProblems()->count()
            ]
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $repository->remove($device);

            $this->addFlash('success', 'devices.remove.success');
            return $this->redirectToRoute('devices');
        }

        return $this->render('devices/remove.html.twig', [
            'form' => $form->createView(),
            'device' => $device
        ]);
    }
}