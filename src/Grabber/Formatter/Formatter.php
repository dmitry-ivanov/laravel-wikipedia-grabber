<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    protected $hasMedia;
    protected $hasGallery;
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
        $this->hasMedia = (bool) $sections->first(function (Section $section) {
            return $section->hasImages();
        });

        $this->hasGallery = (bool) $sections->first(function (Section $section) {
            return $section->hasGallery();
        });

        $this->tocSections = $sections->filter(function (Section $section) {
            return !$section->isMain();
        });
    }

    abstract public function style();

    abstract public function tableOfContents();

    abstract public function section(Section $section);

    protected function hasTableOfContents()
    {
        return $this->tocSections->isNotEmpty();
    }

    protected function sectionId($title)
    {
        return str_slug($title);
    }

    protected function sectionBody(Section $section)
    {
        $body = $section->getBody();

        if (!empty($body)) {
            $body = preg_replace('/(\s*<br.*?>\s*){3,}/m', '$1$1', nl2br($body)) . "\n";
        }

        return $body;
    }

    protected function getLevels()
    {
        return $this->tocSections->map(function (Section $section) {
            return $section->getLevel();
        })->unique()->sort();
    }

    protected function toGallerySize($size)
    {
        return (int) ($size / 1.35);
    }
}
