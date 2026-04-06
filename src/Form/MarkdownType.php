<?php

declare(strict_types=1);

namespace App\Form;

use App\Controller\Markdown\UploadAction;
use Override;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MarkdownType extends TextareaType {

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    )
    {
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver
            /*
             * required must be set to false as the editor only submits its content to the underlying
             * textarea on form submit (which is prevented in case "required" is set to true).
             */
            ->setDefault('required', false)
            ->setDefault('attr', [
                'data-editor' => 'markdown',
                'data-language' => 'de',
                'data-upload' => true,
                'data-csrf-token' => $this->csrfTokenManager->getToken(UploadAction::CsrfTokenId),
                'data-csrf-token-parameter' => UploadAction::CsrfTokenParameter,
                'data-url' => $this->urlGenerator->generate('markdown_upload', [], UrlGeneratorInterface::ABSOLUTE_PATH),
                'data-preview' => $this->urlGenerator->generate('markdown_preview', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ]);
    }
}
