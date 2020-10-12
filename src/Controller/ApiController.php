<?php

namespace App\Controller;

use App\Helper\Status\CurrentStatusHelper;
use App\Response\Room;
use App\Response\Status;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController {
    /**
     * @Route("/status", name="api_status", methods={"GET"})
     */
    public function status(CurrentStatusHelper $currentStatusHelper, UrlGeneratorInterface $urlGenerator) {
        $status = $currentStatusHelper->getCurrentStatus();

        $response = new Status();

        foreach($status->getRoomCategoryStatuses() as $roomCategoryStatus) {
            foreach($roomCategoryStatus->getRoomStatuses() as $roomStatus) {
                $response->addRoom(
                    (new Room())
                        ->setName($roomStatus->getRoom()->getName())
                        ->setNumMaintanance($roomStatus->getMaintenanceCount())
                        ->setNumProblems($roomStatus->getProblemCount())
                        ->setLink($urlGenerator->generate('status_room', ['uuid' => $roomStatus->getRoom()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL))
                );
            }
        }

        return $this->json($response);
    }
}