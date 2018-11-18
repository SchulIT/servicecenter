<?php

namespace App\Converter;

use App\Entity\Problem;
use Symfony\Component\Translation\TranslatorInterface;

class PriorityConverter implements ConverterInterface {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert($value) {
        switch($value) {
            case Problem::PRIORITY_CRITICAL:
                return $this->translator->trans('label.priorities.critical');

            case Problem::PRIORITY_HIGH:
                return $this->translator->trans('label.priorities.high');

            case Problem::PRIORITY_NORMAL:
                return $this->translator->trans('label.priorities.normal');
        }

        throw new \InvalidArgumentException('Invalid priority');
    }
}