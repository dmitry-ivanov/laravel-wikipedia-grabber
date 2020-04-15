<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    /**
     * Create the formatter.
     *
     * @param string $format
     * @param \Illuminate\Support\Collection $sections
     * @return \Illuminated\Wikipedia\Grabber\Formatter\Formatter
     */
    public static function factory(string $format, Collection $sections)
    {
        switch ($format) {
            case 'bulma':
                return new BulmaFormatter($sections);
            case 'bootstrap':
                return new BootstrapFormatter($sections);
            case 'plain':
            default:
                return new PlainFormatter($sections);
        }
    }

    /**
     * Compose the style.
     *
     * @return string
     */
    abstract public function style();

    /**
     * Compose the table of contents.
     *
     * @return string
     */
    abstract public function tableOfContents();

    /**
     * Compose the section.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $section
     * @return string
     */
    abstract public function section(Section $section);
}
