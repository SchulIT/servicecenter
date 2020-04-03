<?php

namespace App\Form;

use App\Entity\ProblemType;
use App\Entity\Room;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class NotificationSettingType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isEnabled', CheckboxType::class, [
                            'label' => 'label.notifications.enabled',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('email', EmailType::class, [
                            'label' => 'label.email',
                            'required' => true
                        ]);
                }
            ])
            ->add('group_settings', FieldsetType::class, [
                'legend' => 'label.notifications.options',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('rooms', EntityType::class, [
                            'class' => Room::class,
                            'choice_label' => 'name',
                            'group_by' => function(Room $room) {
                                return $room->getCategory()->getName();
                            },
                            'required' => false,
                            'multiple' => true,
                            'label' => 'label.rooms'
                        ])
                        ->add('problemTypes', EntityType::class, [
                            'class' => ProblemType::class,
                            'choice_label' => 'name',
                            'group_by' => function(ProblemType $problemType) {
                                return $problemType->getDeviceType()->getName();
                            },
                            'required' => false,
                            'multiple' => true,
                            'label' => 'label.problemtypes'
                        ]);
                }
            ]);

    }
}