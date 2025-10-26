<?php

declare(strict_types=1);

namespace App\Entity;

use Override;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum WikiAccess: string implements TranslatableInterface {
    case All = 'all';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
    case Inherit = 'inherit';

    #[Override]
    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return $translator->trans(
            sprintf('wiki_access.%s', $this->value),
            domain: 'enums',
            locale: $locale
        );
    }
}
