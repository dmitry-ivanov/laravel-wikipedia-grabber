<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/Template:Double_image
 * @see https://ru.wikipedia.org/wiki/Шаблон:Сдвоенное_изображение
 */
class DoubleImageTemplate
{
    /**
     * The body.
     *
     * @var string
     */
    protected $body;

    /**
     * Create a new instance of the template.
     *
     * @param string $body
     * @return void
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Extract from the template.
     *
     * @param string $file
     * @return string
     */
    public function extract(string $file)
    {
        $body = $this->body;
        $body = Str::replaceFirst('{{', '', $body);
        $body = Str::replaceLast('}}', '', $body);

        $parts = explode('|', $body);
        $position = Arr::get($parts, 1);
        $left = Arr::get($parts, 2);
        $right = Arr::get($parts, 4);
        $leftCaption = Arr::get($parts, 6);
        $rightCaption = Arr::get($parts, 7);

        if (empty($leftCaption) && !empty($rightCaption)) {
            $leftCaption = $rightCaption;
        }

        if (empty($rightCaption) && !empty($leftCaption)) {
            $rightCaption = $leftCaption;
        }

        if ($file == $left) {
            return "{$file}|{$position}|{$leftCaption}";
        }

        if ($file == $right) {
            return "{$file}|{$position}|{$rightCaption}";
        }

        return $this->body;
    }
}
