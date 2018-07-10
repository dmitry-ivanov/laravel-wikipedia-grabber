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

    protected function hasTableOfContents()
    {
        return $this->tocSections->isNotEmpty();
    }

    protected function tocLevels()
    {
        return $this->tocSections->map(function (Section $section) {
            return $section->getLevel();
        })->unique()->sort();
    }

    protected function sectionId($title)
    {
        return str_slug($title);
    }

    protected function sectionBody(Section $section)
    {
        return preg_replace('/(\s*<br.*?>\s*){3,}/m', '$1$1', nl2br($section->getBody()));
    }

    protected function toGallerySize($size)
    {
        return (int) ($size / 1.35);
    }

    protected function htmlBlock($open, Collection $items, $close)
    {
        $items = collect(array_map('trim', $items->toArray()))->filter();
        if ($items->isEmpty()) {
            return;
        }

        $open .= !empty($open) ? "\n" : '';
        $close .= !empty($close) ? "\n" : '';

        return "{$open}{$items->implode("\n")}\n{$close}\n";
    }
}
