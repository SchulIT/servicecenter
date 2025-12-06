<?php

namespace App\Controller\Wiki;

use App\Helper\Wiki\WikiSearcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class SearchAction extends AbstractController {
    public const int WIKI_SEARCH_LIMIT = 25;

    #[Route(path: '/wiki/search', name: 'wiki_search')]
    public function __invoke(
        WikiSearcher $wikiSearcher,
        #[MapQueryParameter] string|null $query = null,
        #[MapQueryParameter] int $page = 1,
    ): RedirectResponse|Response {
        if(empty($query)) {
            return $this->redirectToRoute('wiki');
        }

        $result = $wikiSearcher->search($query, $page, self::WIKI_SEARCH_LIMIT);

        return $this->render('wiki/search_results.html.twig', [
            'result' => $result,
            'query' => $query
        ]);
    }
}