<?php

namespace App\Helper\Statistics;

class StatisticsResult {

    /** @var string */
    private $item;

    /** @var int */
    private $num;

    /** @var float */
    private $percentage;

    /**
     * @param string $item
     * @param int $num
     * @param float $percentage
     */
    public function __construct(string $item, int $num, float $percentage) {
        $this->item = $item;
        $this->num = $num;
        $this->percentage = $percentage;
    }

    /**
     * @return string
     */
    public function getItem(): string {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getNum(): int {
        return $this->num;
    }

    /**
     * @return float
     */
    public function getPercentage(): float {
        return $this->percentage;
    }
}