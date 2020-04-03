<?php

namespace App\Form;

use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\RoomRepositoryInterface;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PlacardType extends AbstractType {

    private $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository) {
        $this->roomRepository = $roomRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('room', EntityType::class, [
                            'class' => Room::class,
                            'label' => 'label.room',
                            'query_builder' => function($repository) {
                                return $this->roomRepository->getQueryBuilderForRoomsWithoutPlacard();
                            },
                            'group_by' => function(Room $room) {
                                return $room->getCategory()->getName();
                            },
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('header', TextType::class, [
                            'required' => true,
                            'label' => 'label.header'
                        ]);
                }
            ])
            ->add('devices', CollectionType::class, [
                'entry_type' => PlacardDeviceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'label.devices'
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $placard = $event->getData();
            $form = $event->getForm();

            if($placard->getId() !== null) {
                $groupGeneral = $form->get('group_general');
                $groupGeneral->add('room', EntityType::class, [
                    'class' => Room::class,
                    'label' => 'label.room',
                    'disabled' => true,
                    'data' => $placard->getRoom(),
                    'query_builder' => function($repository) {
                        return $this->roomRepository->getQueryBuilderForRoomsWithPlacard();
                    },
                    'group_by' => function(Room $room) {
                        return $room->getCategory()->getName();
                    }
                ]);
            }
        });
    }
}