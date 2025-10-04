<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use DateTime;
interface HistoryItemInterface {
    public function getDateTime(): DateTime;
}
