<?php

declare(strict_types=1);

namespace App\Converter;

use InvalidArgumentException;
use App\Entity\WikiAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class WikiAccessConverter {

    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function convert($value): string {
        return match ($value) {
            WikiAccess::Admin => $this->translator->trans('label.accesses.admin'),
            WikiAccess::SuperAdmin => $this->translator->trans('label.accesses.super_admin'),
            WikiAccess::All => $this->translator->trans('label.accesses.all'),
            WikiAccess::Inherit => $this->translator->trans('label.accesses.inherit'),
            default => throw new InvalidArgumentException('Unknown WikiArticle::ACCESS value'),
        };
    }
}
