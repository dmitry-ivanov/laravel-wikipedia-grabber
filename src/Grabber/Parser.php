<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;

class Parser
{
    protected $sections;

    public function __construct($title, $body)
    {
        $this->sections = (new SectionsParser($title, $body))->sections();
    }

    public function parse($format)
    {
        $html = '';

        $formatter = Formatter::factory($format);
        foreach ($this->sections as $section) {
            $html .= $formatter->section($section);
        }

        return $html;
    }
}
