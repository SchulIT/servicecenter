<?php

namespace App\Helper\Problems\History;

interface HistoryItemInterface {
    public function getDateTime(): \DateTime;
}