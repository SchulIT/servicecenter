<?php

namespace App\Twig;

use App\Converter\PriorityClassConverter;
use App\Converter\PriorityConverter;
use App\Converter\ProblemConverter;
use App\Converter\PropertyChangedHistoryIconConverter;
use App\Converter\WikiAccessConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtensions extends AbstractExtension {

    public function __construct(private WikiAccessConverter $wikiAccessConverter, private PriorityConverter $priorityConverter, private PriorityClassConverter $priorityClassConverter, private PropertyChangedHistoryIconConverter $historyItemIconConverter, private ProblemConverter $problemConverter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('priority', [ $this->priorityConverter, 'convert' ]),
            new TwigFilter('priority_class', [ $this->priorityClassConverter, 'convert' ]),
            new TwigFilter('access_level', [ $this->wikiAccessConverter, 'convert']),
            new TwigFilter('history_icon', [ $this->historyItemIconConverter, 'convert' ]),
            new TwigFilter('problem', [ $this->problemConverter, 'convert' ])
        ];
    }

}