<?php
namespace Voilab\Restanswer\Interfaces;


use Voilab\Restanswer\Renderer;

interface ContentType {

    public function render($content, Renderer $renderer);

    public function renderError($content, Renderer $renderer);
}