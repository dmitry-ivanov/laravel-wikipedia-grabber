<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

class SectionsParser
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function sections()
    {
        dd('sections', $this->body);
    }
}
