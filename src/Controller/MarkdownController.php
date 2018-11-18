<?php

namespace App\Controller;

use App\Helper\Url\BaseUrlHelper;
use App\Markdown\Markdown;
use EasySlugger\SluggerInterface;
use League\Flysystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkdownController extends Controller {

    private $markdown;
    private $slugger;
    private $filesystem;
    private $baseUrl;

    public function __construct(string $baseUrl, Markdown $markdown, SluggerInterface $slugger, Filesystem $filesystem) {
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
        $result = [ ];
        /** @var UploadedFile[] $files */
        $files = $request->files->all();

        foreach($files as $file) {
            if($file !== null && $file->isValid()) {
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

                $result[] = $url;
            }
        }

        return new JsonResponse($result);
    }
}