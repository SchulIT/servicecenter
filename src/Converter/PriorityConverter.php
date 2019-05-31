<?php

namespace App\Converter;

use App\Entity\Priority;
use App\Entity\Problem;
use Symfony\Contracts\Translation\TranslatorInterface;

class PriorityConverter {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert(Priority $value) {
        $id = sprintf('priority.%s', $value->getValue());

        return $this->translator->trans($id, [ ], 'enums');
    }
}