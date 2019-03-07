<?php

namespace App\Helper\Placards;

use App\Entity\Placard;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PdfExporter {
    private $version;

    public function __construct($version) {
        $this->version = $version;
    }

    public function getPdf(Placard $placard) {
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetCreator(sprintf('ServiceCenter %s', $this->version));
        $pdf->addPage();
        $pdf->SetAutoPageBreak(false);

        $pdf->setFont('Helvetica', '', 32);
        $pdf->Cell(30, 30, utf8_decode(sprintf('Anschlüsse %s (%s)', $placard->getHeader(), $placard->getRoom()->getName())), 0, 1);

        $pdf->setFillColor(204, 255, 204);
        $pdf->setFont('Helvetica', 'B', 22);
        $pdf->Cell(92, 15, 'Signalquelle', 0, 0, 'C', true);

        $pdf->setFillColor(255, 255, 153);
        $pdf->Cell(92, 15, 'Beamer', 0, 0, 'C', true);

        $pdf->setFillColor(204, 255, 255);
        $pdf->Cell(92, 15, 'AV-Receiver', 0, 1, 'C', true);

        $pdf->setFont('Helvetica', '', 22);
        foreach($placard->getDevices() as $device) {
            $pdf->setFillColor(204, 255, 204);
            $pdf->Cell(92, 20, utf8_decode($device->getSource()), 0, 0, 'C', true);

            $pdf->setFillColor(255, 255, 153);
            $pdf->Cell(92, 20, utf8_decode($device->getBeamer()), 0, 0, 'C', true);

            $pdf->setFillColor(204, 255, 255);
            $pdf->Cell(92, 20, utf8_decode($device->getAv()), 0, 1, 'C', true);
        }

        $pdf->SetXY(10, -20);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(145, 12, utf8_decode(sprintf('%s, %s', $placard->getUpdatedBy()->getLastname(), $placard->getUpdatedBy()->getFirstname())));
        $pdf->Cell(132, 12, $placard->getUpdatedAt()->format('d.m.Y'), 0, 1, 'R');

        return $pdf->Output('S'); // return as string
    }

    public function getPdfResponse(Placard $placard) {
        $pdf = $this->getPdf($placard);

        $response = new Response($pdf);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.pdf', $placard->getRoom()->getAlias())
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}