<?php
namespace Voilab\Restanswer;

/**
 * Class Response
 * @package Voilab\Restanswer
 */
class Response {

    public $encoding = 'utf-8';
    public $httpStatus = 200;
    public $content = null;
    public $interrupt = false;
    public $headers = array();
    private $forceEndOfFile = false;

    /**
     * @var Container $container
     */
    public $container;

    /**
     * Response constructor.
     * @param Container $c
     */
    public function __construct(Container $c) {
        $this->container = $c;
    }




    /** ================== Public methods ======================================= */

    /**
     * Fast error helper.
     * Same as doing:
     * $response
     *     ->setHttpStatus($httpStatus)
     *     ->setContent($content)
     *     ->getRenderer()
     *     ->render();
     *
     * @param  integer $httpStatus Status HTTP
     * @param  string  $content    Response content
     * @return Renderer
     */
    public function error($httpStatus, $content) {
        return $this
            ->setHttpStatus($httpStatus)
            ->setContent($content)
            ->getRenderer()
            ->render();
    }

    /**
     * @param string $contentType
     * @return Renderer
     */
    public function getRenderer($contentType = null) {
        $renderer = $this->container[$this->container['config']['engine'] . 'Renderer'];
        $renderer->setResponse($this);
        if ($contentType) {
            $renderer->setContentType($contentType);
        }
        return $renderer;
    }

    /** ================ / Public methods ======================================= */







    /** ================ Accessors ============================================== */

    public function getHttpStatus() {
        return $this->httpStatus;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setHttpStatus($status) {
        $this->httpStatus = $status;
        return $this;
    }

    public function setInterrupt($value) {
        $this->interrupt = $value;
        return $this;
    }

    public function isInterrupt() {
        return $this->interrupt;
    }

    public function getContent() {
        return $this->content;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getEncoding() {
        return $this->encoding;
    }

    /**
     * @param $encoding
     * @return $this
     */
    public function setEncoding($encoding) {
        $this->encoding = $encoding;
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForceEndOfFile()
    {
        return $this->forceEndOfFile;
    }

    /**
     * @param bool $forceEndOfFile
     * @return Response
     */
    public function setForceEndOfFile($forceEndOfFile)
    {
        $this->forceEndOfFile = $forceEndOfFile;
        return $this;
    }

    /** ============== / Accessors ============================================== */

}
