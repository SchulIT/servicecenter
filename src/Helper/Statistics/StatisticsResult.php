<?php

namespace App\Helper\Statistics;

readonly class StatisticsResult {

    public function __construct(private string $item, private int $num, private float $percentage)
    {
    }

    public function getItem(): string {
        return $this->item;
    }

    public function getNum(): int {
        return $this->num;
    }

    public function getPercentage(): float {
        return $this->percentage;
    }
}