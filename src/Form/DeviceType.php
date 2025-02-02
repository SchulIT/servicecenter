<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\Room;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class DeviceType extends AbstractType {
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
                            'label' => 'label.name',
                            'help' => 'label.device_name_help'
                        ])
                        ->add('quantity', IntegerType::class, [
                            'required' => true,
                            'label' => 'label.quantity',
                            'help' => 'label.device_quantity_help',
                            'mapped' => false,
                            'constraints' => [
                                new GreaterThanOrEqual(1)
                            ],
                            'data' => 1
                        ])
                        ->add('start_index', IntegerType::class, [
                            'required' => true,
                            'label' => 'label.start_index',
                            'help' => 'label.device_start_index_help',
                            'mapped' => false,
                            'constraints' => [
                                new GreaterThanOrEqual(0)
                            ],
                            'data' => 0
                        ])
                        ->add('pad_length', IntegerType::class, [
                            'required' => true,
                            'label' => 'label.pad_length',
                            'help' => 'label.device_index_pad_length_help',
                            'mapped' => false,
                            'constraints' => [
                                new GreaterThanOrEqual(0)
                            ],
                            'data' => 0
                        ])
                        ->add('room', EntityType::class, [
                            'class' => Room::class,
                            'choice_label' => 'name',
                            'group_by' => fn(Room $room) => $room->getCategory()->getName(),
                            'required' => true,
                            'label' => 'label.room',
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('type', EntityType::class, [
                            'class' => \App\Entity\DeviceType::class,
                            'choice_label' => 'name',
                            'required' => true,
                            'label' => 'label.devicetype',
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $device = $event->getData();
            $form = $event->getForm();

            if($device !== null && $device instanceof Device && $device->getId() !== null) {
                $form->get('group_general')
                    ->remove('quantity')
                    ->remove('pad_length')
                    ->remove('start_index')
                    ->add('name', TextType::class, [
                        'required' => true,
                        'label' => 'label.name'
                    ]);
            }
        });
    }
}