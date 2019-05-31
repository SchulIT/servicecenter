<?php

namespace App\Helper\Problems\History;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractDefaultPropertyStrategy implements PropertyValueStrategyInterface {

    protected $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    protected abstract function getPropertyName(): string;

    public function supportsProperty(string $name): bool {
        return $name === $this->getPropertyName();
    }

    public function getValue($value) {
        return $value;
    }
}