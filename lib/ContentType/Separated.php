<?php
namespace Voilab\Restanswer\ContentType;

use Voilab\Restanswer\Interfaces\ContentType;
use Voilab\Restanswer\Renderer;

class Separated implements ContentType {

    /**
     * @var string
     */
    public $separator;

    /**
     * @inheritdoc
     */
    public function render($content, Renderer $renderer, $forceEndOfFile = false) {
        if (is_string($content)) {
            return $content;
        } elseif (is_array($content)) {
            $formatted = array_map(function ($line) {
                return implode($this->separator, $line);
            }, $content);

            if ($renderer->getOption('headings', false)) {
                $heading = implode($this->separator, array_keys(array_shift($content)));
                array_unshift($formatted, $heading);
            }
            $linebreak = $renderer->getOption('linebreak', "\n");

            $plainText = implode($linebreak, $formatted);

            if ($forceEndOfFile) {
                $plainText .= $linebreak;
            }

            return $plainText;
        } else {
            return 'Bad format according to the required response Content-Type.';
        }
    }

    /**
     * @inheritdoc
     */
    public function renderError($content, Renderer $renderer) {
        return $this->render($content, $renderer);
    }
}