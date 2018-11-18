<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarkdownType extends TextareaType {
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $generator) {
        $this->urlGenerator = $generator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver
            ->setDefault('attr', [
                'data-editor' => 'markdown',
                'data-language' => 'de',
                'data-upload' => true,
                'data-url' => $this->urlGenerator->generate('markdown_upload', [], UrlGeneratorInterface::ABSOLUTE_PATH),
                'data-preview' => $this->urlGenerator->generate('markdown_preview', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ]);
    }
}