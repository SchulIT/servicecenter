<?php

namespace App\Twig;

use App\Entity\WikiArticle;
use App\Entity\WikiCategory;
use App\Markdown\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class ScExtension extends AbstractExtension {

    private $markdown;

    public function __construct(Markdown $markdown) {
        $this->markdown = $markdown;
    }

    public function getFilters() {
        return [
            new TwigFilter('shorten', [ $this, 'shorten' ]),
            new TwigFilter('markdown', [ $this, 'markdown' ], ['is_safe' => ['html']]),
            new TwigFilter('markdown_short', [ $this, 'markdownShort' ], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('wiki_breadcrumb', [ $this, 'wikiBreadcrumb' ])
        ];
    }

    public function getTests() {
        return [
            new TwigTest('instanceof', [ $this, 'isInstanceOf' ])
        ];
    }

    public function markdown($markdown) {
        $html = $this->markdown->convertToHtml($markdown);
        return $html;
    }

    public function markdownShort($markdown) {
        if(mb_strlen($markdown) > 100) {
            $markdown = mb_substr($markdown, 0, 100) . '…';
        }

        $html = $this->markdown->convertToHtml($markdown);
        return $html;
    }

    public function wikiBreadcrumb($subject) {
        $breadcrumb = [ ];

        while($subject !== null) {
            $item = [
                'name' => $subject->getName()
            ];

            if ($subject instanceof WikiArticle) {
                $item['route'] = 'wiki_article';
                $item['routeParameters'] = [
                    'id' => $subject->getId(),
                    'slug' => $subject->getSlug()
                ];

                $subject = $subject->getCategory();
            } else if($subject instanceof WikiCategory) {
                $item['route'] = 'wiki_category';
                $item['routeParameters'] = [
                    'id' => $subject->getId(),
                    'slug' => $subject->getSlug()
                ];

                $subject = $subject->getParent();
            }

            $breadcrumb[] = $item;
        }

        return array_reverse($breadcrumb);
    }

    public function shorten($string, $length) {
        if(mb_strlen($string) > $length) {
            return mb_substr($string, 0, $length) . '…';
        }

        return $string;
    }

    public function isInstanceOf($var, $instance) {
        $reflectionClass = new \ReflectionClass($instance);
        return $reflectionClass->isInstance($var);
    }
}