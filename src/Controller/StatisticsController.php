<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Form\StatisticsType;
use App\Helper\Statistics\Statistics;
use App\Helper\Statistics\StatisticsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class StatisticsController extends AbstractController {
    #[Route(path: '/statistics', name: 'statistics')]
    public function index(Request $request, StatisticsHelper $statisticsHelper): Response {
        $statistics = new Statistics();
        $form = $this->createForm(StatisticsType::class, $statistics, []);

        $result = null;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $result = $statisticsHelper->getStatistics($statistics);
        }

        return $this->render('statistics/index.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}