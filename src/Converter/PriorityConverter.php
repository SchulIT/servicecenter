<?php

declare(strict_types=1);

namespace App\Converter;

use App\Entity\Priority;
use App\Entity\Problem;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PriorityConverter {

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function convert(Priority $value): string {
        $id = sprintf('priority.%s', $value->value);

        return $this->translator->trans($id, [ ], 'enums');
    }
}
