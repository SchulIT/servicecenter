<?php

namespace App\Markdown\Parser;

use App\Entity\Announcement;
use App\Repository\AnnouncementRepositoryInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AnnouncementParser implements InlineParserInterface {

    public function __construct(private AnnouncementRepositoryInterface $announcementRepository, private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function getCharacters(): array {
        return ['!'];
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

        return true;
    }
}