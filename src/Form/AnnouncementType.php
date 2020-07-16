<?php

namespace App\Form;

use App\Entity\AnnouncementCategory;
use App\Entity\Room;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AnnouncementType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('title', TextType::class, [
                            'required' => true,
                            'label' => 'label.title'
                        ])
                        ->add('category', EntityType::class, [
                            'class' => AnnouncementCategory::class,
                            'label' => 'label.category',
                            'required' => true,
                            'choice_label' => 'name',
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('startDate', DateTimeType::class, [
                            'required' => true,
                            'label' => 'label.start',
                            'time_widget' => 'single_text',
                            'date_widget' => 'single_text'
                        ])
                        ->add('endDate', DateTimeType::class, [
                            'required' => false,
                            'label' => 'label.end',
                            'time_widget' => 'single_text',
                            'date_widget' => 'single_text'
                        ])
                        ->add('details', MarkdownType::class, [
                            'required' => true,
                            'label' => 'label.details'
                        ]);
                }
            ])
            ->add('group_rooms', FieldsetType::class, [
                'legend' => 'label.details',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('rooms', EntityType::class, [
                            'class' => Room::class,
                            'label' => 'label.rooms',
                            'choice_label' => 'name',
                            'required' => true,
                            'multiple' => true,
                            'group_by' => function(Room $room) {
                                return $room->getCategory()->getName();
                            }
                        ]);
                }
            ]);
    }
}