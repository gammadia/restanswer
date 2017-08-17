<?php
namespace Voilab\Restanswer\ContentType;

use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

class Json implements ContentType {

    public function render($content, Renderer $renderer) {
        if ($content) {
            return json_encode($content);
        }
        return null;
    }

    public function renderError($message, Renderer $renderer) {
        return json_encode(array(
            'message' => $message
        ));
    }
}