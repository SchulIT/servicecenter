<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;

class ProblemFilterType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('rooms', EntityType::class, [
                'required' => false,
                'label' => 'label.rooms',
                'placeholder' => 'label.choose.room',
                'class' => Room::class,
                'group_by' => fn(Room $room) => $room->getCategory()->getName(),
                'attr' => [
                    'data-choice' => 'true'
                ],
                'multiple' => true
            ])
            ->add('includeSolved', CheckboxType::class, [
                'required' => false,
                'label' => 'label.filter.solvedproblems',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('includeMaintenance', CheckboxType::class, [
                'required' => false,
                'label' => 'label.filter.maintenance',
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('sortColumn', ChoiceType::class, [
                'required' => true,
                'label' => 'label.sort.label',
                'choices' => [
                    'label.sort.date' => 'createdAt',
                    'label.sort.lastchange' => 'updatedAt',
                    'label.sort.priority' => 'priority'
                ],
                'attr' => [
                    'class' => 'custom-select'
                ]
            ])
            ->add('sortOrder', ChoiceType::class, [
                'required' => true,
                'label' => 'label.sort.order',
                'choices' => [
                    'label.sort.asc' => 'asc',
                    'label.sort.desc' => 'desc'
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('numItems', ChoiceType::class, [
                'required' => false,
                'label' => 'label.filter.items',
                'choices' => [
                    '15' => 15,
                    '25' => 25,
                    '50' => 50,
                    '75' => 75,
                    '100' => 100
                ],
                'attr' => [
                    'class' => 'custom-select'
                ]
            ]);
    }
}