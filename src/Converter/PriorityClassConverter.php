<?php

namespace App\Converter;

use App\Entity\Priority;

class PriorityClassConverter {

    public function convert(Priority $value) {
        $map = [
            'danger' => Priority::Critical(),
            'warning' => Priority::High(),
            'primary' => Priority::Normal()
        ];

        foreach($map as $class => $priority) {
            if($value->equals($priority)) {
                return $class;
            }
        }

        throw new \InvalidArgumentException('Invalid priority');
    }
}