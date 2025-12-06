<?php

namespace App\Controller\Status;

use App\Helper\Status\CurrentStatusHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexAction extends AbstractController {
    #[Route(path: '/status', name: 'current_status')]
    public function __invoke(
        CurrentStatusHelper $currentStatusHelper
    ): Response
    {
        $status = $currentStatusHelper->getCurrentStatus();
        return $this->render('status/index.html.twig', [
            'status' => $status
        ]);
    }
}