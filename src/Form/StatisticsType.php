<?php

namespace App\Form;

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
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_rooms', FieldsetType::class, [
                'legend' => 'label.rooms',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('rooms', EntityType::class, [
                            'label' => 'label.choose.rooms',
                            'class' => Room::class,
                            'group_by' => function(Room $room) {
                                return $room->getCategory()->getName();
                            },
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
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('types', EntityType::class, [
                            'label' => 'label.choose.problemtypes',
                            'class' => ProblemTypeEntity::class,
                            'group_by' => function(ProblemTypeEntity $type) {
                                return $type->getDeviceType()->getName();
                            },
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
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('start', DateType::class, [
                            'label' => 'label.start',
                            'format' => 'd.M.y'
                        ])
                        ->add('end', DateType::class, [
                            'label' => 'label.end',
                            'format' => 'd.M.y'
                        ]);
                }
            ]);

        $builder
            ->add('group_options', FieldsetType::class, [
                'legend' => 'label.statistics.options',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('includeMaintenance', ChoiceType::class, [
                            'label' => 'label.statistics.maintenance',
                            'choices' => [
                                'label.statistics.include' => true,
                                'label.statistics.exclude' => false
                            ],
                            'expanded' => true
                        ])
                        ->add('includeSolved', ChoiceType::class, [
                            'label' => 'label.statistics.solvedproblems',
                            'choices' => [
                                'label.statistics.include' => true,
                                'label.statistics.exclude' => false
                            ],
                            'expanded' => true
                        ])
                        ->add('mode', ChoiceType::class, [
                            'label' => 'label.statistics.evaluate.label',
                            'choices' => [
                                'label.statistics.evaluate.rooms' => Statistics::MODE_ROOMS,
                                'label.statistics.evaluate.problemtypes' => Statistics::MODE_TYPES
                            ],
                            'expanded' => true
                        ]);
                }
            ]);
    }
}