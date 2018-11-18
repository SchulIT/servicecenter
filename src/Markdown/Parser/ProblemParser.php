<?php

namespace App\Markdown\Parser;

use App\Entity\Problem;
use App\Repository\ProblemRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProblemParser extends AbstractInlineParser {

    private $problemRepository;
    private $urlGenerator;

    public function __construct(ProblemRepositoryInterface $problemRepository, UrlGeneratorInterface $urlGenerator) {
        $this->problemRepository = $problemRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function getCharacters() {
        return ['#'];
    }

    /**
     * @inheritDoc
     */
    public function parse(InlineParserContext $inlineContext) {
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
    }
}