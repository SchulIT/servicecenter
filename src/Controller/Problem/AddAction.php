<?php

namespace App\Controller\Problem;

use App\Entity\Problem;
use App\Form\Models\ProblemDto;
use App\Form\ProblemDtoType;
use App\Repository\ProblemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddAction extends AbstractController {
    #[Route(path: '/problems/add', name: 'new_problem')]
    public function __invoke(
        Request $request,
        ProblemRepositoryInterface $problemRepository
    ): RedirectResponse|Response {
        $problemDto = new ProblemDto();

        $form = $this->createForm(ProblemDtoType::class, $problemDto);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $lastUuid = null;
            $problems = 0;

            foreach($problemDto->getDevices() as $device) {
                $problem = (new Problem())
                    ->setContent($problemDto->getContent())
                    ->setProblemType($problemDto->getProblemType())
                    ->setPriority($problemDto->getPriority())
                    ->setDevice($device);

                $problemRepository->persist($problem);
                ++$problems;
                $lastUuid = $problem->getUuid();
            }

            $this->addFlash('success', 'problems.add.success');

            if($problems === 1) {
                return $this->redirectToRoute('show_problem', [
                    'uuid' => $lastUuid
                ]);

            }

            return $this->redirectToRoute('problems');
        }

        return $this->render('problems/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}