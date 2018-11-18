<?php

namespace App\Markdown;

use App\Markdown\Parser\AnnouncementParser;
use App\Markdown\Parser\ProblemParser;
use App\Markdown\Renderer\HeadingRenderer;
use App\Markdown\Renderer\ImageRenderer;
use App\Markdown\Renderer\TableRenderer;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Element\Image;
use Webuni\CommonMark\TableExtension\Table;

class MarkdownExtension extends Extension {

    private $announcementParser;
    private $problemParser;

    private $imageRenderer;
    private $headingRenderer;
    private $tableRenderer;

    public function __construct(AnnouncementParser $announcementParser, ProblemParser $problemParser,
                                ImageRenderer $imageRenderer, HeadingRenderer $headingRenderer, TableRenderer $tableRenderer) {
        $this->announcementParser = $announcementParser;
        $this->problemParser = $problemParser;
        $this->imageRenderer = $imageRenderer;
        $this->headingRenderer = $headingRenderer;
        $this->tableRenderer = $tableRenderer;
    }

    public function getInlineParsers() {
        return [
            $this->announcementParser,
            $this->problemParser
        ];
    }

    public function getInlineRenderers() {
        return [
            Image::class => $this->imageRenderer,
            Heading::class => $this->headingRenderer
        ];
    }

    public function getBlockRenderers() {
        return [
            Table::class => $this->tableRenderer
        ];
    }
}