<?php

namespace App\Controller;

use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MarkdownController extends AbstractController {

    public function __construct(private string $baseUrl, private Markdown $markdown, private SluggerInterface $slugger, private FilesystemOperator $filesystem)
    {
    }

    #[Route(path: '/markdown/preview', name: 'markdown_preview')]
    public function preview(Request $request) {
        $body = $request->getContent();

        $html = $this->markdown->convertToHtml($body);
        return new Response($html);
    }

    #[Route(path: '/markdown/upload', name: 'markdown_upload')]
    public function upload(Request $request) {
        if($request->files->count() !== 1) {
            throw new BadRequestHttpException('You must upload exactly one file at once');
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        if(!$file->isValid()) {
            throw new BadRequestHttpException('Invalid file uploaded');
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $name = substr($name, 0, -strlen($ext)-1);
        do {
            $filename = $this->slugger->uniqueSlugify($name) . '.' . $ext;
        } while($this->filesystem->has($filename));

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream($filename, $stream);
        fclose($stream);

        $url = sprintf('%suploads/%s', $this->baseUrl, $filename);

        return new JsonResponse([
            'filename' => $url
        ]);
    }
}