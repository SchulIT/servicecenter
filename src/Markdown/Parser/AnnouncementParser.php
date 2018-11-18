<?php

namespace App\Markdown\Parser;

use App\Entity\Announcement;
use App\Repository\AnnouncementRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AnnouncementParser extends AbstractInlineParser {

    private $announcementRepository;
    private $urlGenerator;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository, UrlGeneratorInterface $urlGenerator) {
        $this->announcementRepository = $announcementRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function getCharacters() {
        return ['!'];
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

        /** @var Announcement $announcement */
        $announcement = $this->announcementRepository
            ->findOneById($id);

        if($announcement === null) {
            $cursor->restoreState($previousState);
            return false;
        }

        $url = $this->urlGenerator->generate('show_announcement', [ 'id' => $announcement->getId() ]);
        $label = sprintf('!%s', $id);
        $link = new Link($url, $label, $announcement->getTitle());

        $inlineContext->getContainer()->appendChild($link);
    }
}