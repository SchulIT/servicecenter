<?php

namespace App\Form;

use App\Repository\WikiArticleRepositoryInterface;
use App\Wiki\TreeHelper;
use FervoEnumBundle\Generated\Form\WikiAccessType;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WikiArticleType extends AbstractType {

    private $treeHelper;
    private $wikiRepository;

    public function __construct(TreeHelper $treeHelper, WikiArticleRepositoryInterface $wikiRepository) {
        $this->treeHelper = $treeHelper;
        $this->wikiRepository = $wikiRepository;
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
                        ->add('access', WikiAccessType::class, [
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
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.content'
                        ]);
                }
            ]);
    }
}