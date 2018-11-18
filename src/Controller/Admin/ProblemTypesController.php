<?php

namespace App\Controller\Admin;

use App\Entity\DeviceType;
use App\Entity\ProblemType;
use App\Form\ProblemTypeType;
use App\Repository\DeviceRepositoryInterface;
use App\Repository\DeviceTypeRepositoryInterface;
use App\Repository\ProblemTypeRepositoryInterface;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProblemTypesController extends Controller {

    /**
     * @Route("/admin/problemtypes", name="admin_problemtypes")
     */
    public function index(DeviceTypeRepositoryInterface $deviceTypeRepository) {
        $categories = $deviceTypeRepository->findAll();

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'problem_types.add.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/problemtypes/{id}/edit", name="edit_problemtype")
     */
    public function edit(Request $request, ProblemType $type) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProblemTypeType::class, $type, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em->persist($type);
            $em->flush();

            $this->addFlash('success', 'problem_types.edit.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/problemtypes/{id}/remove", name="remove_problemtype")
     */
    public function remove(Request $request, ProblemType $type) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $this->get('translator')->trans('problem_types.remove.confirm', [
                '%name%' => $type->getName(),
                '%count%' => $type->getProblems()->count()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->remove($type);
            $em->flush();

            $this->addFlash('success', 'problem_types.remove.success');
            return $this->redirectToRoute('admin_problemtypes');
        }

        return $this->render('admin/problemtypes/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}