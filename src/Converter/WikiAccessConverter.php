<?php

namespace App\Converter;

use App\Entity\WikiAccess;
use App\Entity\WikiAccessInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WikiAccessConverter implements ConverterInterface {

    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function convert($value) {
        switch($value) {
            case WikiAccess::Admin():
                return $this->translator->trans('label.accesses.admin');

            case WikiAccess::SuperAdmin():
                return $this->translator->trans('label.accesses.super_admin');

            case WikiAccess::All():
                return $this->translator->trans('label.accesses.all');

            case WikiAccess::Inherit():
                return $this->translator->trans('label.accesses.inherit');
        }

        throw new \InvalidArgumentException('Unknown WikiArticle::ACCESS value');
    }
}