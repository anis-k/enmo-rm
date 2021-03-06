<?php

namespace core\Response;

abstract class AbstractResponse
    implements ResponseInterface
{
    use \core\ReadonlyTrait;

    /* -------------------------------------------------------------------------
    - Constants
    ------------------------------------------------------------------------- */

    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    public $mode;

    public $code;

    public $text;

    public $contentType;

    public $contentCount;

    public $language;

    public $headers = [];

    public $body;

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function setMode($mode) 
    {
        $this->mode = $mode;
    }

    public function setCode($code) 
    {
        $this->code = $code;
    }

    public function setText($text) 
    {
        $this->text = $text;
    }

    public function setType($type)
    {
        $this->contentType = $type;
    }

    public function setCount($count)
    {
        $this->contentCount = $count;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function send()
    {
        if (is_scalar($this->body)) {
            echo $this->body. PHP_EOL;
        } elseif (is_resource($this->body)) {
            $output = fopen('php://output', 'w+');
            stream_copy_to_stream($this->body, $output);
            echo PHP_EOL;
        }
    }
}