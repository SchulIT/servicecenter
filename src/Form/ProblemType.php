<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\Problem;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProblemType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver
            ->setDefault('show_options', false);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('general_group', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('device', EntityType::class, [
                            'class' => Device::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('d')
                                    ->select(['d', 'r', 'r'])
                                    ->leftJoin('d.room', 'r')
                                    ->leftJoin('d.type', 't')
                                    ->orderBy('r.name', 'asc')
                                    ->addOrderBy('d.name', 'asc');
                            },
                            'group_by' => function(Device $device) {
                                return $device->getRoom()->getName();
                            },
                            'choice_label' => function(Device $device) {
                                return sprintf('%s (%s)', $device->getName(), $device->getType()->getName());
                            },
                            'label' => 'label.device',
                            'required' => true,
                            'placeholder' => 'label.choose.device',
                            'attr' => [
                                'data-select2-enabled' => 'true'
                            ]
                        ])
                        ->add('problemType', ChoiceType::class, [
                            'choices' => [ ],
                            'label' => 'label.problemtype',
                            'required' => true,
                            'disabled' => true
                        ]);
                }
            ])
            ->add('details_group', FieldsetType::class, [
                'legend' => 'Problem',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('priority', PriorityType::class, [
                            'label' => 'label.priority',
                            'required' => true
                        ])
                        ->add('content', MarkdownType::class, [
                            'required' => true,
                            'label' => 'label.description'
                        ]);
                }
            ]);

        if($options['show_options']) {
            $builder
                ->add('options_group', FieldsetType::class, [
                    'legend' => 'label.wgoptions',
                    'fields' => function(FormBuilderInterface $builder) {
                        $builder
                            ->add('status', StatusChoiceType::class, [
                                'required' => true,
                                'label' => 'label.status'
                            ])
                            ->add('isMaintenance', ChoiceType::class, [
                                'choices' => [
                                    'nein' => false,
                                    'ja' => true
                                ],
                                'expanded' => true,
                                'label' => 'label.maintenance'
                            ]);
                    }
                ]);
        }

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                /** @var Problem $problem */
                $problem = $event->getData();
                $form = $event->getForm();

                if($problem !== null && $problem->getId() !== null) {
                    $form->get('general_group')
                        ->add('device', TextType::class, [
                            'required' => false,
                            'disabled' => true,
                            'mapped' => false,
                            'label' => 'label.device',
                            'data' => sprintf('%s (%s)', $problem->getDevice()->getName(), $problem->getDevice()->getType()->getName())
                        ])
                        ->add('problemType', EntityType::class, [
                            'required' => true,
                            'label' => 'label.problemtype',
                            'class' => \App\Entity\ProblemType::class,
                            'query_builder' => function(EntityRepository $repository) use($problem) {
                                return $repository
                                    ->createQueryBuilder('t')
                                    ->leftJoin('t.deviceType', 'dt')
                                    ->where('dt.id = :id')
                                    ->setParameter('id', $problem->getDevice()->getType()->getId());
                            }
                        ]);
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                /** @var Problem $problem */
                $data = $event->getData();
                $form = $event->getForm();

                $deviceId = $data['general_group']['device'] ?? null;

                if($deviceId !== null) {
                    $form->get('general_group')
                        ->add('problemType', EntityType::class, [
                            'required' => true,
                            'class'=> \App\Entity\ProblemType::class,
                            'query_builder' => function(EntityRepository $repository) use($deviceId) {
                                return $repository->createQueryBuilder('t')
                                    ->leftJoin('t.deviceType', 'dt')
                                    ->leftJoin('dt.devices', 'd')
                                    ->where('d.id = :deviceId')
                                    ->setParameter('deviceId', $deviceId)
                                    ->orderBy('t.name');
                            },
                            'choice_label' => function(\App\Entity\ProblemType $type) {
                                return $type->getName();
                            },
                            'label' => 'label.problemtype',
                            'placeholder' => 'label.choose.problemtype'
                        ]);
                }
            });
    }
}