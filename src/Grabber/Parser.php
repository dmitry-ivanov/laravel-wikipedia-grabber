<?php

namespace Illuminated\Wikipedia\Grabber;

class Parser
{
    protected $body;

    public function __construct($body, $format)
    {
        $this->body = $body;
    }

    public function parse()
    {
        return $this->body;
    }
}