<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;

class Parser
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function parse($format)
    {
        $sections = (new SectionsParser($this->body))->sections();

        dd($sections);

        return $this->body;
    }
}
