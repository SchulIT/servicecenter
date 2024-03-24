<?php

namespace App\Markdown\Parser;

use App\Entity\Problem;
use App\Repository\ProblemRepositoryInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProblemParser implements InlineParserInterface {

    public function __construct(private ProblemRepositoryInterface $problemRepository, private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function getCharacters(): array {
        return ['#'];
    }

    /**
     * @inheritDoc
     */
    public function parse(InlineParserContext $inlineContext): bool {
        $cursor = $inlineContext->getCursor();
        $previousState = $cursor->saveState();

        $cursor->advance();

        $id = $cursor->match('/^[0-9]+(?!\w)/');
        if(empty($id)) {
            $cursor->restoreState($previousState);
            return false;
        }

        /** @var Problem $problem */
        $problem = $this->problemRepository
            ->findOneById($id);

        if($problem === null) {
            $cursor->restoreState($previousState);
            return false;
        }

        $url = $this->urlGenerator->generate('show_problem', [ 'id' => $problem->getId() ]);
        $label = sprintf('#%s', $id);
        $title = sprintf('%s [%s]: %s', $problem->getDevice()->getRoom(), $problem->getDevice(), $problem->getProblemType());
        $link = new Link($url, $label, $title);

        $inlineContext->getContainer()->appendChild($link);

        return true;
    }
}