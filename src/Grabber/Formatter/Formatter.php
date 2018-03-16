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

    protected function sectionTitleTag(array $section)
    {
        $level = $section['level'];

        if ($level > 6) {
            $level = 6;
        }

        return "h{$level}";
    }
}
