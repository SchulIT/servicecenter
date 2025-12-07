<?php

namespace App\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class RedirectToIndexAction extends AbstractController {
    #[Route('')]
    #[Route('/', name: 'index')]
    public function __invoke(): RedirectResponse {
        return $this->redirectToRoute('dashboard');
    }
}