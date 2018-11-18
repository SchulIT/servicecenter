<?php

namespace App\Form;

use App\Entity\Problem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusChoiceType extends ChoiceType {

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $statuses = [
            'label.statuses.open' => Problem::STATUS_OPEN,
            'label.statuses.doing' => Problem::STATUS_DOING,
            'label.statuses.solved' => Problem::STATUS_SOLVED
        ];

        $resolver->setDefault('choices', $statuses);
    }
}