<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\WikiArticle;
use DateTime;
use DateTimeInterface;
use League\CommonMark\ConverterInterface;
use Override;
use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class ScExtension extends AbstractExtension {

    public function __construct(private readonly ConverterInterface $converter)
    {
    }

    #[Override]
    public function getFilters(): array {
        return [
            new TwigFilter('shorten', $this->shorten(...)),
            new TwigFilter('markdown', $this->markdown(...), ['is_safe' => ['html']]),
            new TwigFilter('markdown_short', $this->markdownShort(...), ['is_safe' => ['html']]),
            new TwigFilter('w3cdatetime', $this->w3cDateTime(...))
        ];
    }

    #[Override]
    public function getFunctions(): array {
        return [
            new TwigFunction('wiki_breadcrumb', $this->wikiBreadcrumb(...))
        ];
    }

    #[Override]
    public function getTests(): array {
        return [
            new TwigTest('instanceof', $this->isInstanceOf(...))
        ];
    }

    public function markdown(string $markdown): string {
        return $this->converter->convert($markdown)->getContent();
    }

    public function markdownShort($markdown): string {
        if(mb_strlen((string) $markdown) > 100) {
            $markdown = mb_substr((string) $markdown, 0, 100) . '…';
        }

        return $this->converter->convert($markdown)->getContent();
    }

    public function wikiBreadcrumb(?WikiArticle $subject): array {
        $breadcrumb = [ ];

        while($subject instanceof WikiArticle) {
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
        if(mb_strlen((string) $string) > $length) {
            return mb_substr((string) $string, 0, $length) . '…';
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
