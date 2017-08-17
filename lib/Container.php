<?php

namespace Voilab\Restanswer;

use Voilab\Restanswer\ContentType\Csv;
use Voilab\Restanswer\ContentType\Json;
use Voilab\Restanswer\ContentType\Standard;
use Voilab\Restanswer\ContentType\Tab;
use Voilab\Restanswer\ContentType\Text;
use Voilab\Restanswer\Renderer\Slim;

class Container extends \Pimple\Container {

    /**
     * @param array $config Global configuration
     * @param mixed $engine Engine used for the Rest API
     */
    public function __construct(array $config, $engine)
    {
        parent::__construct();

        $this['config'] = array_merge(array(
            'engine' => 'slim',
            'content-type' => 'application/json',
            'mimetypes' => array(
                'application/json' => 'json',
                'json' => 'json',
                'text/html' => 'string',
                'text/csv' => 'csv',
                'text/tab-separated-values' => 'tab',
                'default' => 'default',
                'standard' => 'default'
            ),
            'codeTranslator' => array(),
            'processorMapping' => array(
                'propertyArrayAccessCheck' => true
            )
        ), $config);

        $this['engine'] = $engine;

        $this['response'] = $this->factory(function ($c) {
            return new Response($c);
        });

        $this['slimRenderer'] = $this->factory(function ($c) {
            return new Slim($c);
        });

        $this['defaultContentType'] = function ($c) {
            return new Standard($c);
        };
        $this['jsonContentType'] = function ($c) {
            return new Json($c);
        };
        $this['csvContentType'] = function ($c) {
            return new Csv($c);
        };
        $this['tabContentType'] = function ($c) {
            return new Tab($c);
        };
        $this['stringContentType'] = function ($c) {
            return new Text($c);
        };

        $this['processor'] = function ($c) {
            return new Processor($c);
        };
    }
}