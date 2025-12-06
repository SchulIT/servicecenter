<?php

namespace App\Controller\Problem;

use App\Helper\Problems\BulkActionManager;
use App\Repository\ProblemRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class BulkAction extends AbstractController {
    public const string BULK_CSRF_TOKEN_ID = 'problem_bulk';

    #[Route(path: '/problems/bulk', name: 'admin_problems_bulk', methods: ['POST'])]
    public function __invoke(
        Request $request,
        ProblemRepositoryInterface $problemRepository,
        BulkActionManager $bulkActionManager,
        TranslatorInterface $translator
    ): RedirectResponse {
        if(!$this->isCsrfTokenValid(self::BULK_CSRF_TOKEN_ID, $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'problems.bulk.csrf');

            return $this->redirectToRoute('problems');
        }

        $action = $request->request->get('action');

        if(!$bulkActionManager->canRunAction($action)) {
            throw new BadRequestHttpException();
        }

        $ids = explode(',', $request->request->get('uuids'));


        $problems = $problemRepository
            ->findByUuids($ids);

        if(count($problems) > 0) {
            $bulkActionManager->run($problems, $action);
            $this->addFlash('success', $translator->trans('problems.bulk.success', ['%count%' => count($problems)]));
        }

        return $this->redirectToRoute('problems');
    }
}