<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    protected $tocSections;

    public static function factory($format, Collection $sections)
    {
        switch ($format) {
            case 'bulma':
                return new BulmaFormatter($sections);

            case 'plain':
            default:
                return new PlainFormatter($sections);
        }
    }

    public function __construct(Collection $sections)
    {
        $this->tocSections = $sections->filter(function (Section $section) {
            return !$section->isMain();
        });
    }

    abstract public function style();

    abstract public function tableOfContents();

    abstract public function section(Section $section);

    protected function sectionId($title)
    {
        return str_slug($title);
    }

    protected function getLevels()
    {
        return $this->tocSections->map(function (Section $section) {
            return $section->getLevel();
        })->unique()->sort();
    }
}
