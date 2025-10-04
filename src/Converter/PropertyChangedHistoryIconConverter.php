<?php

declare(strict_types=1);

namespace App\Converter;

use App\Helper\Problems\History\PropertyChangedHistoryItem;

class PropertyChangedHistoryIconConverter {

    private array $map = [
        'assignee' => 'bullhorn',
        'priority' => 'fire',
        'isOpen' => 'asterisk',
        'content' => 'info-circle',
        'problemType' => 'tag',
        'isMaintenance' => 'wrench'
    ];

    public function convert(PropertyChangedHistoryItem $item): string {
        $icon = 'null';

        if(isset($this->map[$item->getProperty()])) {
            $icon = $this->map[$item->getProperty()];
        }

        return 'fas fa-' . $icon;
    }
}
