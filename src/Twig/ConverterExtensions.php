<?php

namespace App\Twig;

use App\Converter\PriorityClassConverter;
use App\Converter\PriorityConverter;
use App\Converter\PropertyChangedHistoryIconConverter;
use App\Converter\WikiAccessConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtensions extends AbstractExtension {

    private $wikiAccessConverter;
    private $priorityConverter;
    private $priorityClassConverter;
    private $historyItemIconConverter;

    public function __construct(WikiAccessConverter $wikiAccessConverter, PriorityConverter $priorityConverter,
                                PriorityClassConverter $priorityClassConverter, PropertyChangedHistoryIconConverter $historyItemIconConverter) {
        $this->wikiAccessConverter = $wikiAccessConverter;
        $this->priorityConverter = $priorityConverter;
        $this->priorityClassConverter = $priorityClassConverter;
        $this->historyItemIconConverter = $historyItemIconConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('priority', [ $this->priorityConverter, 'convert' ]),
            new TwigFilter('priority_class', [ $this->priorityClassConverter, 'convert' ]),
            new TwigFilter('access_level', [ $this->wikiAccessConverter, 'convert']),
            new TwigFilter('history_icon', [ $this->historyItemIconConverter, 'convert' ])
        ];
    }

}