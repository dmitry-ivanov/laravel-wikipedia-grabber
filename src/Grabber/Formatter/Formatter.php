<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

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

    abstract public function section(array $section);
}
