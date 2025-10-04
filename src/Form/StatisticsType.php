<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use App\Entity\ProblemType as ProblemTypeEntity;
use App\Entity\Room;
use App\Helper\Statistics\Statistics;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class StatisticsType extends AbstractType {
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_rooms', FieldsetType::class, [
                'legend' => 'label.rooms',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('rooms', EntityType::class, [
                            'label' => 'label.choose.rooms',
                            'class' => Room::class,
                            'group_by' => fn(Room $room): ?string => $room->getCategory()->getName(),
                            'multiple' => true,
                            'choice_label' => 'name',
                            'attr' => [
                                'size' => 10
                            ]
                        ]);
                }
            ]);

        $builder
            ->add('group_types', FieldsetType::class, [
                'legend' => 'label.problemtypes',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('types', EntityType::class, [
                            'label' => 'label.choose.problemtypes',
                            'class' => ProblemTypeEntity::class,
                            'group_by' => fn(ProblemTypeEntity $type): ?string => $type->getDeviceType()->getName(),
                            'multiple' => true,
                            'choice_label' => 'name',
                            'attr' => [
                                'size' => 10
                            ]
                        ]);
                }
            ]);

        $builder
            ->add('group_range', FieldsetType::class, [
                'legend' => 'label.timewindow',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('start', DateType::class, [
                            'label' => 'label.start',
                            'widget' => 'single_text'
                        ])
                        ->add('end', DateType::class, [
                            'label' => 'label.end',
                            'widget' => 'single_text'
                        ]);
                }
            ]);

        $builder
            ->add('group_options', FieldsetType::class, [
                'legend' => 'label.statistics.options',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('includeMaintenance', ChoiceType::class, [
                            'label' => 'label.statistics.maintenance',
                            'choices' => [
                                'label.statistics.include' => true,
                                'label.statistics.exclude' => false
                            ],
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ]
                        ])
                        ->add('includeSolved', ChoiceType::class, [
                            'label' => 'label.statistics.solvedproblems',
                            'choices' => [
                                'label.statistics.include' => true,
                                'label.statistics.exclude' => false
                            ],
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ]
                        ])
                        ->add('mode', ChoiceType::class, [
                            'label' => 'label.statistics.evaluate.label',
                            'choices' => [
                                'label.statistics.evaluate.rooms' => Statistics::MODE_ROOMS,
                                'label.statistics.evaluate.problemtypes' => Statistics::MODE_TYPES
                            ],
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ]
                        ]);
                }
            ]);
    }
}
