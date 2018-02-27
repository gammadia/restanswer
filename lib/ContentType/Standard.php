<?php
namespace Voilab\Restanswer\ContentType;


use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

class Standard implements ContentType {

    /**
     * @inheritdoc
     */
    public function render($content, Renderer $renderer, $forceEndOfFile = false) {
        return $content;
    }

    /**
     * @inheritdoc
     */
    public function renderError($content, Renderer $renderer) {
        return $content;
    }
}