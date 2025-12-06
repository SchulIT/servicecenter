<?php

namespace App\Controller\Admin\DeviceType;

use App\Entity\DeviceType;
use App\Form\DeviceTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    public function __construct(private readonly DeviceTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/devicetypes/add', name: 'add_devicetype')]
    public function __invoke(Request $request): RedirectResponse|Response {
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
}