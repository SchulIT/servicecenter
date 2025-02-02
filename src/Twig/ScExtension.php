<?php

namespace App\Twig;

use DateTime;
use DateTimeInterface;
use League\CommonMark\ConverterInterface;
use ReflectionClass;
use App\Entity\WikiArticle;
use App\Markdown\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class ScExtension extends AbstractExtension {

    public function __construct(private readonly ConverterInterface $converter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('shorten', [ $this, 'shorten' ]),
            new TwigFilter('markdown', [ $this, 'markdown' ], ['is_safe' => ['html']]),
            new TwigFilter('markdown_short', [ $this, 'markdownShort' ], ['is_safe' => ['html']]),
            new TwigFilter('w3cdatetime', [ $this, 'w3cDateTime' ])
        ];
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('wiki_breadcrumb', [ $this, 'wikiBreadcrumb' ])
        ];
    }

    public function getTests(): array {
        return [
            new TwigTest('instanceof', [ $this, 'isInstanceOf' ])
        ];
    }

    public function markdown($markdown): string {
        dump($this->converter);
        return $this->converter->convert($markdown);
    }

    public function markdownShort($markdown): string {
        if(mb_strlen($markdown) > 100) {
            $markdown = mb_substr($markdown, 0, 100) . '…';
        }

        return $this->converter->convert($markdown);
    }

    public function wikiBreadcrumb(?WikiArticle $subject): array {
        $breadcrumb = [ ];

        while($subject !== null) {
            $item = [
                'name' => $subject->getName()
            ];

            $item['route'] = 'wiki_article';
            $item['routeParameters'] = [
                'uuid' => $subject->getUuid(),
                'slug' => $subject->getSlug()
            ];

            $subject = $subject->getParent();

            $breadcrumb[] = $item;
        }

        return array_reverse($breadcrumb);
    }

    public function shorten($string, $length): string {
        if(mb_strlen($string) > $length) {
            return mb_substr($string, 0, $length) . '…';
        }

        return $string;
    }

    public function w3cDateTime(DateTime $dateTime): string {
        return $dateTime->format(DateTimeInterface::W3C);
    }

    public function isInstanceOf($var, $instance): bool {
        $reflectionClass = new ReflectionClass($instance);
        return $reflectionClass->isInstance($var);
    }
}