<?php

namespace App\Markdown;

use App\Markdown\Parser\AnnouncementParser;
use App\Markdown\Parser\ProblemParser;
use App\Markdown\Processors\ImageProcessor;
use App\Markdown\Renderer\HeadingRenderer;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Ext\Table\Table;
use App\Markdown\Renderer\TableRenderer;
use League\CommonMark\Ext\Table\TableCaption;
use League\CommonMark\Ext\Table\TableCaptionRenderer;
use League\CommonMark\Ext\Table\TableCell;
use League\CommonMark\Ext\Table\TableCellRenderer;
use League\CommonMark\Ext\Table\TableParser;
use League\CommonMark\Ext\Table\TableRow;
use League\CommonMark\Ext\Table\TableRowRenderer;
use League\CommonMark\Ext\Table\TableSection;
use League\CommonMark\Ext\Table\TableSectionRenderer;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Inline\Element\Image;

class MarkdownExtension implements ExtensionInterface {

    private $announcementParser;
    private $problemParser;

    private $imageProcessor;
    private $headingRenderer;
    private $tableRenderer;

    public function __construct(AnnouncementParser $announcementParser, ProblemParser $problemParser,
                                ImageProcessor $imageRenderer, HeadingRenderer $headingRenderer, TableRenderer $tableRenderer) {
        $this->announcementParser = $announcementParser;
        $this->problemParser = $problemParser;
        $this->imageProcessor = $imageRenderer;
        $this->headingRenderer = $headingRenderer;
        $this->tableRenderer = $tableRenderer;
    }

    public function register(ConfigurableEnvironmentInterface $environment) {
        $environment
            ->addBlockParser(new TableParser())
            ->addBlockRenderer(Heading::class, $this->headingRenderer)
            ->addBlockRenderer(Table::class, $this->tableRenderer)
            ->addBlockRenderer(TableCaption::class, new TableCaptionRenderer())
            ->addBlockRenderer(TableSection::class, new TableSectionRenderer())
            ->addBlockRenderer(TableRow::class, new TableRowRenderer())
            ->addBlockRenderer(TableCell::class, new TableCellRenderer())
            ->addInlineParser($this->announcementParser)
            ->addInlineParser($this->problemParser)
            ->addEventListener(DocumentParsedEvent::class, [ $this->imageProcessor, 'onDocumentParsed' ] , 0);
    }
}