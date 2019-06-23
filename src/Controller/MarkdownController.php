<?php

namespace App\Controller;

use App\Helper\Url\BaseUrlHelper;
use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MarkdownController extends AbstractController {

    private $markdown;
    private $slugger;
    private $filesystem;
    private $baseUrl;

    public function __construct(string $baseUrl, Markdown $markdown, SluggerInterface $slugger, FilesystemInterface $filesystem) {
        $this->markdown = $markdown;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @Route("/markdown/preview", name="markdown_preview")
     */
    public function preview(Request $request) {
        $body = $request->getContent();

        $html = $this->markdown->convertToHtml($body);
        return new Response($html);
    }

    /**
     * @Route("/markdown/upload", name="markdown_upload")
     */
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