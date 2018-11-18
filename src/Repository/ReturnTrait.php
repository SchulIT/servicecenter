<?php

namespace App\Repository;

trait ReturnTrait {
    protected function returnFirstOrNull($result) {
        if(count($result) > 0) {
            return $result[0];
        }

        return null;
    }
}