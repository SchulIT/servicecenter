<?php

namespace App\Converter;

use App\Entity\WikiAccessInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WikiAccessConverter implements ConverterInterface {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert($value) {
        switch($value) {
            case WikiAccessInterface::ACCESS_ADMIN:
                return $this->translator->trans('label.accesses.admin');

            case WikiAccessInterface::ACCESS_AG:
                return $this->translator->trans('label.accesses.wg');

            case WikiAccessInterface::ACCESS_ALL:
                return $this->translator->trans('label.accesses.all');

            case WikiAccessInterface::ACCESS_INHERIT:
                return $this->translator->trans('label.accesses.inherit');
        }

        throw new \InvalidArgumentException('Unknown WikiArticle::ACCESS value');
    }
}