<?php

namespace App\Helper\Problems\History;

use DateTime;
interface HistoryItemInterface {
    public function getDateTime(): DateTime;
}