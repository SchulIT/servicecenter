<?php

namespace App\Markdown;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Markdown {
    private $converter;
    private $cache;

    public function __construct(CommonMarkConverter $converter, AdapterInterface $cache) {
        $this->converter = $converter;
        $this->cache = $cache;
    }

    public function convertToHtml($markdown) {
        $hash = hash('sha512', $markdown);
        $key = sprintf('markdown.%s', $hash);

        $item = $this->cache->getItem($key);

        if(!$item->isHit()) {
            $html = $this->converter->convertToHtml($markdown);
            $item->set($html);
            $this->cache->save($item);
        }

        return $item->get();
    }
}