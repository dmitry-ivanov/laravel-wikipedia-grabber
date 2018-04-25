<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;

class Parser
{
    protected $sections;

    public function __construct($title, $body, array $images = null)
    {
        $this->sections = (new SectionsParser($title, $body))->sections();
        $this->sections = (new SectionsRemoveEmpty($this->sections))->filter();
        $this->sections = (new SectionsRemoveBoring($this->sections))->filter();
    }

    public function parse($format)
    {
        $formatter = Formatter::factory($format, $this->sections);
        $html = $formatter->style();

        foreach ($this->sections as $section) {
            $html .= $formatter->section($section);

            if ($section->isMain()) {
                $html .= $formatter->tableOfContents();
            }
        }

        return $html;
    }

    public function getSections()
    {
        return $this->sections;
    }
}
