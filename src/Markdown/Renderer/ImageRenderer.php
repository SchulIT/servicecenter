<?php

namespace App\Markdown\Renderer;

use League\CommonMark\ElementRendererInterface;
use League\CommonMark\Inline\Element\AbstractInline;

class ImageRenderer extends \League\CommonMark\Inline\Renderer\ImageRenderer {
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer) {
        $html = parent::render($inline, $htmlRenderer);

        $html->setAttribute('class', 'img-responsive');
        $html->setAttribute('margin', 'margin:5px 0px;');

        return $html;
    }
}