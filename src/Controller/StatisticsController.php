<?php

namespace App\Controller;

use App\Form\StatisticsType;
use App\Helper\Statistics\Statistics;
use App\Helper\Statistics\StatisticsHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
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

        dump($result);

        return $this->render('statistics/index.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}