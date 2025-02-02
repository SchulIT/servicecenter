<?php

namespace App\Form;

use App\Entity\RoomCategory;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'required' => true,
                            'label' => 'label.name'
                        ])
                        ->add('category', EntityType::class, [
                            'required' => true,
                            'label' => 'label.category',
                            'class' => RoomCategory::class,
                            'choice_label' => 'name',
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ]);
    }
}