<?php

namespace App\Markdown;

use App\Markdown\Parser\AnnouncementParser;
use App\Markdown\Parser\ProblemParser;
use App\Markdown\Processors\ImageProcessor;
use App\Markdown\Renderer\HeadingRenderer;
use App\Markdown\Renderer\TableRenderer;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableCellRenderer;
use League\CommonMark\Extension\Table\TableParser;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableRowRenderer;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Extension\Table\TableSectionRenderer;

class MarkdownExtension implements ExtensionInterface {

    public function __construct(private AnnouncementParser $announcementParser, private ProblemParser $problemParser, private ImageProcessor $imageProcessor, private HeadingRenderer $headingRenderer, private TableRenderer $tableRenderer)
    {
    }

    public function register(ConfigurableEnvironmentInterface $environment): void {
        $environment
            ->addBlockParser(new TableParser())
            ->addBlockRenderer(Heading::class, $this->headingRenderer, 100)
            ->addBlockRenderer(Table::class, $this->tableRenderer)
            ->addBlockRenderer(TableSection::class, new TableSectionRenderer())
            ->addBlockRenderer(TableRow::class, new TableRowRenderer())
            ->addBlockRenderer(TableCell::class, new TableCellRenderer())
            ->addInlineParser($this->announcementParser)
            ->addInlineParser($this->problemParser)
            ->addEventListener(DocumentParsedEvent::class, [ $this->imageProcessor, 'onDocumentParsed' ] , 0);
    }
}