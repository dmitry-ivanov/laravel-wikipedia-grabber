<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

/**
 * @see https://en.wikipedia.org/wiki/Template:Multiple_image
 * @see https://ru.wikipedia.org/wiki/Шаблон:Кратное_изображение
 * @see https://ru.wikipedia.org/wiki/Шаблон:Фотоколонка
 * @see https://ru.wikipedia.org/wiki/Шаблон:Фотоколонка+
 * @see https://en.wikipedia.org/wiki/Template:Listen
 */
class MultipleImageTemplate
{
    /**
     * The body.
     */
    protected string $body;

    /**
     * Indicates whether the given template is "Listen" or not.
     */
    protected bool $isListen;

    /**
     * Create a new instance of the template.
     */
    public function __construct(string $body)
    {
        $this->body = $body;
        $this->isListen = $this->isListen();
    }

    /**
     * Extract from the template.
     */
    public function extract(string $file): string
    {
        $result = collect();

        $parts = $this->explode();
        $index = $this->getIndex($parts, $file);
        foreach ($parts as $part) {
            if (!$this->isExtractingPart($part, $index)) {
                continue;
            }

            $part = $this->removeIndex($part, $index);
            $part = $this->transformPosition($part);
            if (empty($part)) {
                continue;
            }

            $result->push($part);
        }

        return "{{{$result->implode('|')}}}";
    }

    /**
     * Check whether the given template is "Listen" or not.
     */
    protected function isListen(): bool
    {
        return Str::startsWith(mb_strtolower($this->body, 'utf-8'), '{{listen');
    }

    /**
     * Explode the body.
     */
    protected function explode(): array
    {
        $body = $this->body;

        $body = Str::replaceFirst('{{', '', $body);
        $body = Str::replaceLast('}}', '', $body);
        $body = (new Wikitext($body))->plain();

        return array_map('trim', explode('|', $body));
    }

    /**
     * Get the index.
     */
    protected function getIndex(array $parts, string $file): int
    {
        $index = 1;

        foreach ($parts as $part) {
            if (Str::contains($part, $file)) {
                return $index;
            }

            if ($this->isFileName($part)) {
                $index++;
            }
        }

        return 0;
    }

    /**
     * Check whether the given part should be extracted or not.
     */
    protected function isExtractingPart(string $part, int $index): bool
    {
        if (!$this->isSomeParameter($part)) {
            return true;
        }

        if ($this->isListen && $this->isNotIndexed($part) && ($index > 1)) {
            return false;
        }

        return (bool) preg_match("/[^\d\s]+({$index}){0,1}(\s*?)=/", $part);
    }

    /**
     * Check whether the given part is not indexed.
     */
    protected function isNotIndexed(string $part): bool
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^filename(\s*)=(.+?)/', $part) || preg_match('/^имя файла(\s*)=(.+?)/', $part)
            || preg_match('/^title(\s*)=(.+?)/', $part) || preg_match('/^название(\s*)=(.+?)/', $part)
            || preg_match('/^description(\s*)=(.+?)/', $part) || preg_match('/^описание(\s*)=(.+?)/', $part);
    }

    /**
     * Remove the index.
     */
    protected function removeIndex(string $part, int $index): string
    {
        if (!$this->isSomeParameter($part)) {
            return $part;
        }

        $parts = array_map('trim', explode('=', $part));
        $parts[0] = Str::replaceLast((string) $index, '', $parts[0]);

        return implode('=', $parts);
    }

    /**
     * Transform the position.
     */
    protected function transformPosition(string $part): string
    {
        $lowercased = mb_strtolower($part, 'utf-8');
        if (!preg_match('/^(align|pos|float|зона)=/', $lowercased)) {
            return $part;
        }

        return last(explode('=', $part));
    }

    /**
     * Check whether the given string is some parameter or not.
     */
    protected function isSomeParameter(string $string): bool
    {
        return preg_match('/^(\S+)(\s*?)(\S*)(\s*?)=/', $string)
            || preg_match('/^(\d+)(\s*)%$/', $string);
    }

    /**
     * Check whether the given part is filename or not.
     *
     * @see https://www.mediawiki.org/wiki/Help:Images#Supported_media_types_for_images
     */
    protected function isFileName(string $part): bool
    {
        $part = mb_strtolower($part, 'utf-8');

        $extensions = collect([
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'ogg', 'oga', 'ogv', 'pdf', 'djvu', 'tiff', 'mp3', 'wav', 'mp4', 'webm',
        ])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return Str::endsWith($part, $extensions);
    }
}
