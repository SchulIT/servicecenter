<?php

namespace App\Controller;

use App\Form\StatisticsType;
use App\Helper\Statistics\Statistics;
use App\Helper\Statistics\StatisticsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController {
    /**
     * @Route("/statistics", name="statistics")
     */
    public function index(Request $request, StatisticsHelper $statisticsHelper) {
        $statistics = new Statistics();
        $form = $this->createForm(StatisticsType::class, $statistics, []);

        $result = null;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /** @var StatisticsHelper $helper */
            $result = $statisticsHelper->getStatistics($statistics);
        }

        return $this->render('statistics/index.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}