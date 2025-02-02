<?php

namespace App\Helper\Problems\History;

use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractDefaultPropertyStrategy implements PropertyValueStrategyInterface {

    use UserDisplayNameTrait;

    public function __construct(protected readonly TranslatorInterface $translator) {    }

    protected abstract function getPropertyName(): string;

    public function supportsProperty(string $name): bool {
        return $name === $this->getPropertyName();
    }

    public function getValue($value) {
        return $value;
    }


}