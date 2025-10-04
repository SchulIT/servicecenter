<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoomCategoryType extends AbstractType {
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
                        ]);
                }
            ]);
    }
}
