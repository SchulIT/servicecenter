<?php

namespace App\Converter;

use App\Entity\Problem;

class PriorityClassConverter implements ConverterInterface {

    public function convert($value) {
        switch($value) {
            case Problem::PRIORITY_CRITICAL:
                return 'danger';

            case Problem::PRIORITY_HIGH:
                return 'warning';

            case Problem::PRIORITY_NORMAL:
                return 'primary';
        }

        throw new \InvalidArgumentException('Invalid priority');
    }
}