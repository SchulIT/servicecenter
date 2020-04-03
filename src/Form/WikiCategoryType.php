<?php

namespace App\Form;

use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WikiCategoryType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('general_group', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function (FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name',
                            'required' => true
                        ])
                        ->add('access', WikiAccessChoiceType::class, [
                            'label' => 'label.access',
                            'required' => true,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ]);
    }

}