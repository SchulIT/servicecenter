<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Priority;
use App\Entity\ProblemType;
use App\Entity\Device;
use App\Entity\Problem;
use App\Entity\Room;
use Doctrine\ORM\EntityRepository;
use FervoEnumBundle\Generated\Form\PriorityType;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProblemDtoType extends AbstractType {

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('general_group', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('room', EntityType::class, [
                            'class' => Room::class,
                            'query_builder' => fn(EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('r')
                                ->addOrderBy('r.name', 'asc'),
                            'group_by' => fn(Room $room): ?string => $room->getCategory()->getName(),
                            'choice_label' => fn(Room $room): ?string => $room->getName(),
                            'label' => 'label.room',
                            'mapped' => false,
                            'placeholder' => 'label.choose.room',
                            'attr' => [
                                'data-choice' => 'true',
                                'data-trigger' => 'ajax',
                                'data-url' => $this->urlGenerator->generate('devices_ajax'),
                                'data-target' => '#problem_dto_general_group_devices',
                                'data-paramname' => 'room'
                            ]
                        ])
                        ->add('devices', ChoiceType::class, [
                            'choices' => [ ],
                            'label' => 'label.device',
                            'required' => true,
                            'disabled' => true,
                            'placeholder' => 'label.choose.device',
                            'attr' => [
                                'data-choice' => 'true',
                                'data-trigger' => 'ajax',
                                'data-url' => $this->urlGenerator->generate('problem_ajax'),
                                'data-target' => '#problem_dto_general_group_problemType',
                                'data-paramname' => 'device',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ],
                            'multiple' => true,
                            'by_reference' => false
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
                'fields' => function(FormBuilderInterface $builder): void {
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event): void {
                /** @var Problem $data */
                $data = $event->getData();
                $form = $event->getForm();

                $roomId = $data['general_group']['room'] ?? null;
                $deviceIds = $data['general_group']['devices'] ?? null;

                if($roomId !== null) {
                    $form->get('general_group')
                        ->add('devices', EntityType::class, [
                            'class' => Device::class,
                            'query_builder' => fn(EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('d')
                                ->leftJoin('d.room', 'r')
                                ->where('r.id = :id')
                                ->setParameter('id', $roomId)
                                ->orderBy('d.name'),
                            'choice_label' => fn(Device $device): ?string => $device->getName(),
                            'label' => 'label.device',
                            'placeholder' => 'label.choose.device',
                            'multiple' => true,
                            'attr' => [
                                'data-choice' => 'true',
                                'data-trigger' => 'ajax',
                                'data-url' => $this->urlGenerator->generate('problem_ajax'),
                                'data-target' => '#problem_dto_general_group_problemType',
                                'data-paramname' => 'device',
                                'data-existing-url' => $this->urlGenerator->generate('existing_problems_ajax')
                            ],
                            'by_reference' => false
                        ]);
                }

                if($deviceIds !== null && is_array($deviceIds) && count($deviceIds) > 0) {
                    $form->get('general_group')
                        ->add('problemType', EntityType::class, [
                            'required' => true,
                            'class'=> ProblemType::class,
                            'query_builder' => fn(EntityRepository $repository): QueryBuilder => $repository->createQueryBuilder('t')
                                ->leftJoin('t.deviceType', 'dt')
                                ->leftJoin('dt.devices', 'd')
                                ->where('d.id = :deviceId')
                                ->setParameter('deviceId', $deviceIds[0])
                                ->orderBy('t.name'),
                            'choice_label' => fn(ProblemType $type): ?string => $type->getName(),
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
