<?php

namespace App\Controller\Admin;

use App\Entity\ProblemType;
use App\Form\ProblemTypeType;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\ProblemTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProblemTypesController extends AbstractController {

    private $repository;

    public function __construct(ProblemTypeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin/problemtypes", name="admin_problemtypes")
     */
    public function index(DeviceTypeRepositoryInterface $deviceTypeRepository) {
        $categories = $deviceTypeRepository
            ->findAll();

        return $this->render('admin/problemtypes/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/problemtypes/add", name="add_problemtype")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/admin/problemtypes/{uuid}/edit", name="edit_problemtype")
     */
    public function edit(Request $request, ProblemType $type) {
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

    /**
     * @Route("/admin/problemtypes/{uuid}/remove", name="remove_problemtype")
     */
    public function remove(Request $request, ProblemType $type) {
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