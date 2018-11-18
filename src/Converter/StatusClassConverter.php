<?php

namespace App\Converter;

use App\Entity\Problem;

class StatusClassConverter implements ConverterInterface {

    public function convert($value) {
        switch($value) {
            case Problem::STATUS_OPEN:
                return 'danger';

            case Problem::STATUS_DOING:
                return 'primary';

            case Problem::STATUS_SOLVED:
                return 'success';
        }

        throw new \InvalidArgumentException('Invalid status');
    }
}