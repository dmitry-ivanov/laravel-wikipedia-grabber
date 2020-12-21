<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsAddImages;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsInPreview;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveBoring;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveEmpty;

class Parser
{
    protected $sections;

    public function __construct($title, $body, array $images = null, $isPreview = false)
    {
        $sections = (new SectionsParser($title, $body))->sections();

        $sections = (new SectionsInPreview($sections, $isPreview))->pipe();
        $sections = (new SectionsAddImages($sections, $images))->pipe();
        $sections = (new SectionsRemoveEmpty($sections))->pipe();
        $sections = (new SectionsRemoveBoring($sections))->pipe();

        $this->sections = $sections;
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
