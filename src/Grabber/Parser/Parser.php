<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;

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
