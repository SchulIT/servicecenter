<?php

namespace App\Markdown\Processors;

use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Image;

class ImageProcessor {
    public function __construct(private EnvironmentInterface $environment)
    {
    }

    public function onDocumentParsed(DocumentParsedEvent $event) {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Image) || !$event->isEntering()) {
                continue;
            }

            $node->data['attributes']['class'] = 'img-fluid';
            $node->data['attributes']['style'] = 'margin: 5px 0px;';
        }
    }
}
