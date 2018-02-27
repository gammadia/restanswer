<?php

namespace Voilab\Restanswer\Interfaces;

use Voilab\Restanswer\Renderer;

/**
 * Interface ContentType
 * @package Voilab\Restanswer\Interfaces
 */
interface ContentType
{

    /**
     * @param $content
     * @param Renderer $renderer
     * @param bool $forceEndOfFile
     * @return mixed
     */
    public function render($content, Renderer $renderer, $forceEndOfFile = false);

    /**
     * @param $content
     * @param Renderer $renderer
     * @return mixed
     */
    public function renderError($content, Renderer $renderer);
}
