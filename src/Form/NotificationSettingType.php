<?php

namespace App\Form;

use App\Entity\NotificationSetting;
use App\Entity\ProblemType;
use App\Entity\Room;
use App\Entity\User;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
                        ->add('email', TextType::class, [
                            'label' => 'label.email',
                            'required' => false,
                            'mapped' => false,
                            'disabled' => true
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
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $settings = $event->getData();

                if($settings instanceof NotificationSetting) {
                    $form->get('group_general')
                        ->get('email')
                        ->setData($settings->getUser()->getEmail());
                }
            });
    }
}