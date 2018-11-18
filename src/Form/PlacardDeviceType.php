<?php

namespace App\Form;

use App\Entity\PlacardDevice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlacardDeviceType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('source', TextType::class, [
                'required' => true,
                'label' => 'label.source',
                'attr' => [
                    'class' => 'input-sm'
                ]
            ])
            ->add('beamer', TextType::class, [
                'required' => true,
                'label' => 'label.beamer',
                'attr' => [
                    'class' => 'input-sm'
                ]
            ])
            ->add('av', TextType::class, [
                'required' => true,
                'label' => 'label.av',
                'attr' => [
                    'class' => 'input-sm'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => PlacardDevice::class,
        ));
    }
}