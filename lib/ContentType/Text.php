<?php
namespace Voilab\Restanswer\ContentType;

use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

/**
 * Class Text
 * @package Voilab\Restanswer\ContentType
 */
class Text implements ContentType
{
    public function render($content, Renderer $renderer, $newLineEOF = false)
    {
        if (is_string($content)) {
            return $content;
        } elseif (is_object($content)) {
            if (method_exists($content, 'toString')) {
                return $content->toString();
            }
            return sprintf('Instance of %s', get_class($content));
        } else {
            return 'Bad format according to the required response Content-Type.';
        }
    }

    public function renderError($content, Renderer $renderer)
    {
        return $this->render($content, $renderer);
    }
}
