<?php

namespace App\Markdown\Processors;

use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Image;

class ImageProcessor {
    private $environment;

    public function __construct(EnvironmentInterface $environment) {
        $this->environment = $environment;
    }

    public function onDocumentParsed(DocumentParsedEvent $event) {
        $document = $event->getDocument();
        $walker = $document->walker();

        while($event = $walker->next()) {
            $node = $event->getNode();

            if(!($node instanceof Image) || !$event->isEntering()) {
                continue;
            }

            $node->data['attributes']['class'] = 'img-responsive';
            $node->data['attributes']['style'] = 'margin: 5px 0px;';
        }
    }
}