<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\Priority;
use App\Entity\Problem;
use Doctrine\ORM\EntityRepository;
use FervoEnumBundle\Generated\Form\PriorityType;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProblemType extends AbstractType {

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('general_group', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('device', EntityType::class, [
                            'class' => Device::class,
                            'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('d')
                                ->select(['d', 'r', 'r'])
                                ->leftJoin('d.room', 'r')
                                ->leftJoin('d.type', 't')
                                ->orderBy('r.name', 'asc')
                                ->addOrderBy('d.name', 'asc'),
                            'group_by' => fn(Device $device) => $device->getRoom()->getName(),
                            'choice_label' => fn(Device $device) => sprintf('%s (%s) [%s]', $device->getName(), $device->getType()->getName(), $device->getRoom()->getName()),
                            'label' => 'label.device',
                            'required' => true,
                            'placeholder' => 'label.choose.device',
                            'attr' => [
                                'data-choice' => 'true',
                                'data-trigger' => 'ajax',
                                'data-url' => $this->urlGenerator->generate('problem_ajax'),
                                'data-target' => '#problem_general_group_problemType',
                                'data-paramname' => 'device',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ]
                        ])
                        ->add('problemType', ChoiceType::class, [
                            'choices' => [ ],
                            'label' => 'label.problemtype',
                            'required' => true,
                            'disabled' => true,
                            'attr' => [
                                'data-choice' => 'true',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ]
                        ]);
                }
            ])
            ->add('details_group', FieldsetType::class, [
                'legend' => 'Problem',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('priority', EnumType::class, [
                            'class' => Priority::class,
                            'label' => 'label.priority',
                            'required' => true,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.description'
                        ]);
                }
            ]);

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
                            'query_builder' => fn(EntityRepository $repository) => $repository
                                ->createQueryBuilder('t')
                                ->leftJoin('t.deviceType', 'dt')
                                ->where('dt.id = :id')
                                ->setParameter('id', $problem->getDevice()->getType()->getId()),
                            'attr' => [
                                'data-choice' => 'true',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ]
                        ]);
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                /** @var Problem $data */
                $data = $event->getData();
                $form = $event->getForm();

                $deviceId = $data['general_group']['device'] ?? null;

                if($deviceId !== null) {
                    $form->get('general_group')
                        ->add('problemType', EntityType::class, [
                            'required' => true,
                            'class'=> \App\Entity\ProblemType::class,
                            'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('t')
                                ->leftJoin('t.deviceType', 'dt')
                                ->leftJoin('dt.devices', 'd')
                                ->where('d.id = :deviceId')
                                ->setParameter('deviceId', $deviceId)
                                ->orderBy('t.name'),
                            'choice_label' => fn(\App\Entity\ProblemType $type) => $type->getName(),
                            'label' => 'label.problemtype',
                            'placeholder' => 'label.choose.problemtype',
                            'attr' => [
                                'data-choice' => 'true',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ]
                        ]);
                }
            });
    }
}