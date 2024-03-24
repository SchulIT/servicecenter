<?php

namespace App\Converter;

use InvalidArgumentException;
use App\Entity\Priority;

class PriorityClassConverter {

    public function convert(Priority $value): string {
        $map = [
            'danger' => Priority::Critical,
            'warning' => Priority::High,
            'primary' => Priority::Normal
        ];

        foreach($map as $class => $priority) {
            if($value === $priority) {
                return $class;
            }
        }

        throw new InvalidArgumentException('Invalid priority');
    }
}