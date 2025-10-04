<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use App\Entity\DeviceType;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProblemTypeType extends AbstractType {
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
                        ->add('name', TextType::class, [
                            'required' => true,
                            'label' => 'label.name'
                        ])
                        ->add('deviceType', EntityType::class, [
                            'required' => true,
                            'label' => 'label.devicetype',
                            'class' => DeviceType::class,
                            'choice_label' => 'name',
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ]);
    }
}
