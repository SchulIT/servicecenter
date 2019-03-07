<?php

namespace App\Controller;

use App\Entity\Placard;
use App\Entity\Room;
use App\Form\PlacardType;
use App\Helper\Placards\PdfExporter;
use App\Repository\PlacardRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class PlacardsController extends AbstractController {

    private $repository;

    public function __construct(PlacardRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * @Route("/placards", name="placards")
     */
    public function index() {
        $placards = $this->repository
            ->findAll();

        return $this->render('placards/index.html.twig', [
            'placards' => $placards
        ]);
    }

    /**
     * @Route("/placards/add", name="add_placard")
     */
    public function add(Request $request) {
        $placard = new Placard();

        $form = $this->createForm(PlacardType::class, $placard, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $placard->setUpdatedBy($this->getUser());

            $this->repository->persist($placard);

            $this->addFlash('success', 'placards.add.success');
            return $this->redirectToRoute('placards');
        }

        return $this->render('placards/add.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/placards/{alias}/edit", name="edit_placard")
     */
    public function edit(Request $request, Room $room) {
        /** @var Placard $placard */
        $placard = $this->repository
            ->findOneByRoom($room);

        if($placard === null) {
            throw new NotFoundHttpException();
        }

        $originalDevices = $placard->getDevices()->toArray();

        $form = $this->createForm(PlacardType::class, $placard, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $placard->setUpdatedBy($this->getUser());

            $this->repository->persist($placard, $originalDevices);

            $this->addFlash('success', 'placards.add.success');
            return $this->redirectToRoute('placards');
        }

        return $this->render('placards/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/placards/{alias}/pdf", name="pdf_placard")
     */
    public function pdf(Request $request, Room $room, PdfExporter $pdfExporter) {
        /** @var Placard $placard */
        $placard = $this->repository
            ->findOneByRoom($room);

        if($placard === null) {
            throw new NotFoundHttpException();
        }

        return $pdfExporter->getPdfResponse($placard);
    }
}