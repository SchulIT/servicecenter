<?php

declare(strict_types=1);

namespace App\Form;

use Override;
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
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder): void {
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
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('rooms', EntityType::class, [
                            'class' => Room::class,
                            'choice_label' => 'name',
                            'group_by' => fn(Room $room): ?string => $room->getCategory()->getName(),
                            'required' => false,
                            'multiple' => true,
                            'label' => 'label.rooms'
                        ])
                        ->add('problemTypes', EntityType::class, [
                            'class' => ProblemType::class,
                            'choice_label' => 'name',
                            'group_by' => fn(ProblemType $problemType): ?string => $problemType->getDeviceType()->getName(),
                            'required' => false,
                            'multiple' => true,
                            'label' => 'label.problemtypes'
                        ]);
                }
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event): void {
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
