<?php

namespace Voilab\Restanswer;

/**
 * Class Response
 * @package Voilab\Restanswer
 */
class Response
{
    /** @var string */
    public $encoding = 'utf-8';

    /** @var int */
    public $httpStatus = 200;

    /** @var mixed */
    public $content = null;

    /** @var bool */
    public $interrupt = false;

    /** @var array<string, mixed> */
    public $headers = array();

    /** @var bool */
    private $newLineEOF = false;

    /**
     * @var Container $container
     */
    public $container;

    /**
     * Response constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
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
    public function error($httpStatus, $content)
    {
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
    public function getRenderer($contentType = null)
    {
        $renderer = $this->container[$this->container['config']['engine'] . 'Renderer'];
        $renderer->setResponse($this);
        if ($contentType) {
            $renderer->setContentType($contentType);
        }
        return $renderer;
    }

    /** ================ / Public methods ======================================= */







    /** ================ Accessors ============================================== */

    /**
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setHttpStatus($status)
    {
        $this->httpStatus = $status;
        return $this;
    }

    /**
     * @param bool $value
     * @return self
     */
    public function setInterrupt($value)
    {
        $this->interrupt = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInterrupt()
    {
        return $this->interrupt;
    }

    /**
     * @return null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array<string, mixed> $headers
     * @return self
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNewLineEOF()
    {
        return $this->newLineEOF;
    }

    /**
     * @param bool $newLineEOF
     * @return Response
     */
    public function setNewLineEOF($newLineEOF)
    {
        $this->newLineEOF = $newLineEOF;
        return $this;
    }

    /** ============== / Accessors ============================================== */
}
