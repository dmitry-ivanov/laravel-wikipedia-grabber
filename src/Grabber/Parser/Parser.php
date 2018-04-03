<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;

class Parser
{
    protected $sections;

    public function __construct($title, $body)
    {
        $this->sections = (new SectionsParser($title, $body))->sections();
        $this->sections = (new SectionsRemoveEmpty($this->sections))->filter();
        $this->sections = (new SectionsRemoveBoring($this->sections))->filter();
    }

    public function parse($format)
    {
        $html = '';

        $formatter = Formatter::factory($format);
        $html .= $formatter->style();

        foreach ($this->sections as $section) {
            $html .= $formatter->section($section);

            if ($section->isMain()) {
                $html .= $formatter->tableOfContents($this->sections);
            }
        }

        return $html;
    }
}
