<?php

namespace Voilab\Restanswer\ContentType;

use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

/**
 * Class Standard
 * @package Voilab\Restanswer\ContentType
 */
class Standard implements ContentType
{

    /**
     * @inheritdoc
     */
    public function render($content, Renderer $renderer, $newLineEOF = false)
    {
        return $content;
    }

    /**
     * @inheritdoc
     */
    public function renderError($content, Renderer $renderer)
    {
        return $content;
    }
}
