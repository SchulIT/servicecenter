<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use App\Entity\WikiAccess;
use App\Repository\WikiArticleRepositoryInterface;
use App\Wiki\TreeHelper;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WikiArticleType extends AbstractType {

    public function __construct(private readonly TreeHelper $treeHelper, private readonly WikiArticleRepositoryInterface $wikiRepository)
    {
    }

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('general_group', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name',
                            'required' => true
                        ])
                        ->add('parent', ChoiceType::class, [
                            'label' => 'label.parent',
                            'choices' => $this->treeHelper->flattenTree($this->wikiRepository->findAll()),
                            'placeholder'=> ' / ',
                            'required' => false,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ])
                        ->add('access', EnumType::class, [
                            'class' => WikiAccess::class,
                            'label' => 'label.access',
                            'required' => true,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ])
            ->add('content_group', FieldsetType::class, [
                'legend' => 'label.content',
                'fields' => function(FormBuilderInterface $builder): void {
                    $builder
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.content'
                        ]);
                }
            ]);
    }
}
