<?php

namespace App\Form;

use App\Entity\Problem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriorityType extends ChoiceType {

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $priorities = [
            'label.priorities.normal' => Problem::PRIORITY_NORMAL,
            'label.priorities.high' => Problem::PRIORITY_HIGH,
            'label.priorities.critical' => Problem::PRIORITY_CRITICAL
        ];

        $resolver->setDefault('choices', $priorities);
    }

}