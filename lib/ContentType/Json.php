<?php
namespace Voilab\Restanswer\ContentType;

use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

/**
 * Class Json
 * @package Voilab\Restanswer\ContentType
 */
class Json implements ContentType
{
    /**
     * @inheritdoc
     */
    public function render($content, Renderer $renderer, $newLineEOF = false)
    {
        if ($content) {
            return json_encode($content);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function renderError($message, Renderer $renderer)
    {
        return json_encode(array(
            'message' => $message
        ));
    }
}
