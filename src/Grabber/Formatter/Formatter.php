<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class Formatter
{
    /**
     * Create the formatter.
     */
    public static function factory(string $format, Collection $sections): self
    {
        return match ($format) {
            'bulma' => new BulmaFormatter($sections),
            'bootstrap' => new BootstrapFormatter($sections),
            default => new PlainFormatter($sections),
        };
    }

    /**
     * Compose the style.
     */
    abstract public function style(): string;

    /**
     * Compose the table of contents.
     */
    abstract public function tableOfContents(): string;

    /**
     * Compose the section.
     */
    abstract public function section(Section $section): string;
}
