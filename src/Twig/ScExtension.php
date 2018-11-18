<?php

namespace App\Twig;

use App\Entity\WikiArticle;
use App\Entity\WikiCategory;
use App\Markdown\Markdown;

class ScExtension extends \Twig_Extension {

    private $markdown;

    public function __construct(Markdown $markdown) {
        $this->markdown = $markdown;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('shorten', [ $this, 'shorten' ]),
            new \Twig_SimpleFilter('markdown', [ $this, 'markdown' ], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('markdown_short', [ $this, 'markdownShort' ], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('wiki_breadcrumb', [ $this, 'wikiBreadcrumb' ])
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
}