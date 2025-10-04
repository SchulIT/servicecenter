<?php

declare(strict_types=1);

namespace App\Helper\Problems\History;

use Override;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractDefaultPropertyStrategy implements PropertyValueStrategyInterface {

    use UserDisplayNameTrait;

    public function __construct(protected readonly TranslatorInterface $translator) {    }

    protected abstract function getPropertyName(): string;

    #[Override]
    public function supportsProperty(string $name): bool {
        return $name === $this->getPropertyName();
    }

    #[Override]
    public function getValue($value) {
        return $value;
    }


}
