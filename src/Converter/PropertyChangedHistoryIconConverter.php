<?php

namespace App\Converter;

use App\Helper\Problems\History\PropertyChangedHistoryItem;

class PropertyChangedHistoryIconConverter {

    private $map = [
        'assignee' => 'bullhorn',
        'priority' => 'fire',
        'isOpen' => 'asterisk',
        'content' => 'info-circle',
        'problemType' => 'tag',
        'isMaintenance' => 'wrench'
    ];

    public function convert(PropertyChangedHistoryItem $item): string {
        return 'fa fa-' . $this->map[$item->getProperty()] ?? 'null';
    }
}