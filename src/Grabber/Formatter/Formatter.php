<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    public static function factory($format)
    {
        switch ($format) {
            case 'bulma':
                return new BulmaFormatter;

            case 'plain':
            default:
                return new PlainFormatter;
        }
    }

    abstract public function style(Collection $sections);

    abstract public function tableOfContents(Collection $sections);

    abstract public function section(Section $section);
}
