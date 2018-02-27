<?php
namespace Voilab\Restanswer;


/**
 * Class Renderer
 * @package Voilab\Restanswer
 */
abstract class Renderer {

    /**
     * @var Response
     */
    public $response;
    public $contentType;
    private $content;
    public $status = 200;
    public $options = [];

    public $container;



    abstract public function engineRender($interrupt = false);
    abstract public function setHeader($key, $value);




/** ================== Constructor ========================================== */

    /**
     * Renderer constructor.
     * @param Container $c
     */
    public function __construct(Container $c) {
        $this->container = $c;
        $this->contentType = $c['config']['content-type'];
    }

/** ================ / Constructors ========================================= */








/** ================== Public methods ======================================= */

    public function prepare() {
        $this->prepareContent();
        $this->prepareHeaders();
        return $this;
    }

    public function convert($from, $to) {
        $content = $this->content;
        $content = iconv($from, $to, $content);
        $this->content = $content;
        return $this;
    }

    public function render() {
        $this->prepare();
        $this->engineRender($this->getResponse()->isInterrupt());
    }

    public function getContent() {
        return $this->content;
    }

/** ================ / Public methods ======================================= */









/** ================ Accessors ============================================== */

    public function getContentType() {
        return $this->contentType;
    }

    public function setContentType($type) {
        $this->contentType = $type;
        return $this;
    }

    public function setResponse(Response $response) {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Récupération d'une option par son nom
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null) {
        if (!isset($this->options[$name])) {
            return $default;
        }
        return $this->options[$name];
    }

    /**
     * Définition d'une option pour le moteur de rendu
     *
     * @param string $name
     * @param mixed $value
     * @return Renderer
     */
    public function setOption($name, $value) {
        $this->options[$name] = $value;
        return $this;
    }

/** ============== / Accessors ============================================== */








/** ================== Private methods ====================================== */

    protected function getContentTypeAdapter() {
        if (isset($this->container['config']['mimetypes'][$this->contentType])) {
            return $this->container['config']['mimetypes'][$this->contentType] . 'ContentType';
        }

        return $this->container['config']['mimetypes']['default'] . 'ContentType';
    }

    /**
     * Préparation du contenu
     */
    protected function prepareContent() {
        $content = $this->response->getContent();
        $this->status = $this->response->getHttpStatus();

        if ($this->status >= 200 && $this->status < 400) {
            $this->content = $this->container[$this->getContentTypeAdapter()]->render($content, $this, $this->response->isForceEndOfFile());
        } else {
            $this->content = $this->container[$this->getContentTypeAdapter()]->renderError($content, $this);
        }
    }

    /**
     * Préparation des headers
     */
    protected function prepareHeaders() {
        // format de retour
        $this->setHeader('Content-Type', $this->contentType . '; charset=' . $this->response->getEncoding());

        foreach ($this->response->headers as $key => $value) {
            $this->setHeader($key, $value);
        }

        // caching
        $this->setHeader('ETag', sha1($this->content));
    }

/** ================ / Private methods ====================================== */

}