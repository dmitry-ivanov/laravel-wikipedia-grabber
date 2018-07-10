<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    public static function factory($format, Collection $sections)
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

    abstract public function style();

    abstract public function tableOfContents();

    abstract public function section(Section $section);
}
