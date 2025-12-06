<?php

namespace App\Controller\Markdown;

use EasySlugger\SluggerInterface;
use League\CommonMark\ConverterInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreviewAction extends AbstractController {

    public function __construct(
        private readonly ConverterInterface $converter)
    {
    }

    #[Route(path: '/markdown/preview', name: 'markdown_preview')]
    public function __invoke(Request $request): Response {
        $body = $request->getContent();

        $html = $this->converter->convert($body);
        return new Response($html->getContent());
    }
}