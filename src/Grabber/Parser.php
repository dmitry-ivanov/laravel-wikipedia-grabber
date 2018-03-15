<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;

class Parser
{
    protected $title;
    protected $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function parse($format)
    {
        $sections = (new SectionsParser($this->title, $this->body))->sections();

        dd($sections);

        return $this->body;
    }
}
