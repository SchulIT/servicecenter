<?php

namespace App\Form;

use App\Entity\WikiAccess;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WikiAccessChoiceType extends ChoiceType {
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $accessTypes = [
            'label.accesses.inherit' => WikiAccess::Inherit(),
            'label.accesses.all' => WikiAccess::All(),
            'label.accesses.admin' => WikiAccess::Admin(),
            'label.accesses.super_admin' => WikiAccess::SuperAdmin()
        ];

        $resolver->setDefault('choices', $accessTypes);
    }
}