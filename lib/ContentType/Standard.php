<?php
namespace Voilab\Restanswer\ContentType;


use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

class Standard implements ContentType {

    public function render($content, Renderer $renderer) {
        return $content;
    }

    public function renderError($content, Renderer $renderer) {
        return $content;
    }
}