<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProblemType;
use App\Form\ProblemTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\PaginationQuery;
use App\Repository\ProblemTypeRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class ProblemTypesController extends AbstractController {

    public function __construct(private readonly ProblemTypeRepositoryInterface $repository)
    {
    }

    #[Route(path: '/admin/problemtypes', name: 'admin_problemtypes')]
    public function index(DeviceTypeRepositoryInterface $deviceTypeRepository, #[MapQueryParameter] int $page = 1, #[MapQueryParameter(name: 'dt')] string|null $deviceTypeUuid = null): Response {
        $deviceType = null;

        if($deviceTypeUuid !== null) {
            $deviceType = $deviceTypeRepository->findOneByUuid($deviceTypeUuid);
        }

        return $this->render('admin/problemtypes/index.html.twig', [
            'types' => $this->repository->findAllPaginated(new PaginationQuery(page: $page), deviceType: $deviceType),
            'deviceType' => $deviceType,
            'deviceTypes' => $deviceTypeRepository->findAll()
        ]);
    }

    #[Route(path: '/admin/problemtypes/add', name: 'add_problemtype')]
    public function add(Request $request): RedirectResponse|Response {
        $type = new ProblemType();
        $form = $this->createForm(ProblemTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'problem_types.add.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/admin/problemtypes/{uuid}/edit', name: 'edit_problemtype')]
    public function edit(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ProblemType $type): RedirectResponse|Response {
        $form = $this->createForm(ProblemTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($type);

            $this->addFlash('success', 'problem_types.edit.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }

    #[Route(path: '/admin/problemtypes/{uuid}/remove', name: 'remove_problemtype')]
    public function remove(Request $request, #[MapEntity(mapping: ['uuid' => 'uuid'])] ProblemType $type): RedirectResponse|Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'problem_types.remove.confirm',
            'message_parameters' => [
                '%name%' => $type->getName(),
                '%num%' => $type->getProblems()->count()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($type);

            $this->addFlash('success', 'problem_types.remove.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type
        ]);
    }
}
