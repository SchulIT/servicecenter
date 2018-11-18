<?php

namespace App\Converter;

use App\Entity\Problem;
use Symfony\Component\Translation\TranslatorInterface;

class StatusConverter implements ConverterInterface {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert($value) {
        switch($value) {
            case Problem::STATUS_OPEN:
                return $this->translator->trans('label.statuses.open');

            case Problem::STATUS_DOING:
                return $this->translator->trans('label.statuses.doing');

            case Problem::STATUS_SOLVED:
                return $this->translator->trans('label.statuses.solved');
        }

        throw new \InvalidArgumentException('Invalid status');
    }
}