<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsAddImages;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveBoring;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveEmpty;

class Parser
{
    protected $sections;

    public function __construct($title, $body, array $images = null)
    {
        $this->sections = (new SectionsParser($title, $body))->sections();
        $this->sections = (new SectionsAddImages($this->sections, $images))->pipe();
        $this->sections = (new SectionsRemoveEmpty($this->sections))->pipe();
        $this->sections = (new SectionsRemoveBoring($this->sections))->pipe();
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
