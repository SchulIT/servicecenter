<?php

namespace App\Controller;

use App\Entity\Placard;
use App\Entity\Room;
use App\Form\PlacardType;
use App\Helper\Placards\PdfExporter;
use App\Repository\PlacardRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PlacardsController extends Controller {

    private $placardsRepository;

    public function __construct(PlacardRepositoryInterface $placardRepository) {
        $this->placardsRepository = $placardRepository;
    }

    /**
     * @Route("/placards", name="placards")
     */
    public function index(PlacardRepositoryInterface $placardRepository) {
        $placards = $placardRepository
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

            $em = $this->getDoctrine()->getManager();
            $em->persist($placard);
            $em->flush();

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
        $placard = $this->placardsRepository
            ->findOneByRoom($room);

        if($placard === null) {
            throw new NotFoundHttpException();
        }

        $originalDevices = new ArrayCollection($placard->getDevices()->toArray());

        $form = $this->createForm(PlacardType::class, $placard, [ ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $placard->setUpdatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();

            foreach($originalDevices as $device) {
                if($placard->getDevices()->contains($device) !== true) {
                    $em->remove($device);
                }
            }

            foreach($placard->getDevices() as $device) {
                $em->persist($device);
            }

            $em->persist($placard);
            $em->flush();

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
        $placard = $this->placardsRepository
            ->findOneByRoom($room);

        if($placard === null) {
            throw new NotFoundHttpException();
        }

        return $pdfExporter->getPdfResponse($placard);
    }
}