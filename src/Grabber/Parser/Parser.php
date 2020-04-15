<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Formatter\Formatter;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsAddImages;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsInPreview;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveBoring;
use Illuminated\Wikipedia\Grabber\Parser\Pipe\SectionsRemoveEmpty;

class Parser
{
    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * Create a new instance of the Parser.
     *
     * @param string $title
     * @param string $body
     * @param array|null $images
     * @param bool $isPreview
     * @return void
     */
    public function __construct(string $title, string $body, array $images = null, bool $isPreview = false)
    {
        $sections = (new SectionsParser($title, $body))->sections();

        $sections = (new SectionsInPreview($sections, $isPreview))->pipe();
        $sections = (new SectionsAddImages($sections, $images))->pipe();
        $sections = (new SectionsRemoveEmpty($sections))->pipe();
        $sections = (new SectionsRemoveBoring($sections))->pipe();

        $this->sections = $sections;
    }

    /**
     * Parse according to the given format.
     *
     * @param string $format
     * @return string
     */
    public function parse(string $format)
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

    /**
     * Get the sections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }
}
