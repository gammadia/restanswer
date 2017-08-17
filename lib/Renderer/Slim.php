<?php
namespace Voilab\Restanswer\Renderer;


use Voilab\Restanswer\Renderer;

class Slim extends Renderer {

    public function engineRender($interrupt = false) {
        if ($interrupt) {
            $this->container['engine']->halt($this->status, $this->getContent());
        } else {
            $this->container['engine']->response()->setStatus($this->status);
            $this->container['engine']->response()->write($this->getContent());
        }
    }

    public function setHeader($key, $value) {
        $this->container['engine']->response()->header($key, $value);
    }
}