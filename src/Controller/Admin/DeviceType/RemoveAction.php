<?php

namespace App\Controller\Admin\DeviceType;

use App\Entity\DeviceType;
use App\Repository\DeviceTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveAction extends AbstractController {
    public function __construct(private readonly DeviceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/devicetypes/{uuid}/remove', name: 'remove_devicetype')]
    #[NotFoundRedirect(redirectRoute: 'admin_devicetypes', flashMessage: 'device_types.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'admin_devicetypes', flashMessage: 'device_types.not_found')]
    public function __invoke(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] DeviceType $type, TranslatorInterface $translator): Response {
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