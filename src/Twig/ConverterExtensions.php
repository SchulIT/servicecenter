<?php

namespace App\Twig;

use App\Converter\PriorityClassConverter;
use App\Converter\PriorityConverter;
use App\Converter\StatusClassConverter;
use App\Converter\StatusConverter;
use App\Converter\WikiAccessConverter;

class ConverterExtensions extends \Twig_Extension {

    private $wikiAccessConverter;
    private $priorityConverter;
    private $priorityClassConverter;
    private $statusConverter;
    private $statusClassConverter;

    public function __construct(WikiAccessConverter $wikiAccessConverter, PriorityConverter $priorityConverter,
                                PriorityClassConverter $priorityClassConverter, StatusConverter $statusConverter,
                                StatusClassConverter $statusClassConverter) {
        $this->wikiAccessConverter = $wikiAccessConverter;
        $this->priorityConverter = $priorityConverter;
        $this->priorityClassConverter = $priorityClassConverter;
        $this->statusConverter = $statusConverter;
        $this->statusClassConverter = $statusClassConverter;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('priority', [ $this, 'priority' ]),
            new \Twig_SimpleFilter('status', [ $this, 'status' ]),
            new \Twig_SimpleFilter('priority_class', [ $this, 'priorityClass' ]),
            new \Twig_SimpleFilter('status_class', [ $this, 'statusClass' ]),
            new \Twig_SimpleFilter('access_level', [ $this, 'accessLevel'])
        ];
    }

    public function accessLevel($access) {
        return $this->wikiAccessConverter->convert($access);
    }

    public function priority($priority) {
        return $this->priorityConverter->convert($priority);
    }

    public function status($status) {
        return $this->statusConverter->convert($status);
    }

    public function priorityClass($priority) {
        return $this->priorityClassConverter->convert($priority);
    }

    public function statusClass($status) {
        return $this->statusClassConverter->convert($status);
    }
}