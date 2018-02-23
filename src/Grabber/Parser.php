<?php

namespace Illuminated\Wikipedia\Grabber;

class Parser
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function parse($format)
    {
        return $this->body;
    }
}
