<?php

namespace App\Form;

use App\Entity\WikiAccessInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WikiAccessChoiceType extends ChoiceType {
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $accessTypes = [
            'label.accesses.inherit' => WikiAccessInterface::ACCESS_INHERIT,
            'label.accesses.all' =>WikiAccessInterface::ACCESS_ALL,
            'label.accesses.wg' =>WikiAccessInterface::ACCESS_WG,
            'label.accesses.admin' => WikiAccessInterface::ACCESS_ADMIN
        ];

        $resolver->setDefault('choices', $accessTypes);
    }
}