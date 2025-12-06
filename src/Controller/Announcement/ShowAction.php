<?php

namespace App\Controller\Announcement;

use App\Entity\Announcement;
use SchulIT\CommonBundle\Http\Attribute\ForbiddenRedirect;
use SchulIT\CommonBundle\Http\Attribute\NotFoundRedirect;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShowAction extends AbstractController {
    #[Route(path: '/announcements/{uuid}', name: 'show_announcement')]
    #[NotFoundRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    #[ForbiddenRedirect(redirectRoute: 'announcements', flashMessage: 'announcements.not_found')]
    public function __invoke(#[MapEntity(mapping: ['uuid' => 'uuid'])] Announcement $announcement): Response {
        return $this->render('announcements/show.html.twig', [
            'announcement' => $announcement
        ]);
    }
}