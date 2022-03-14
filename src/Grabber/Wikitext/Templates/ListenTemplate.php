<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

use Illuminate\Support\Str;

/**
 * @see https://en.wikipedia.org/wiki/Template:Listen
 * @see https://ru.wikipedia.org/wiki/Шаблон:Listen
 */
class ListenTemplate
{
    /**
     * The body.
     */
    protected string $body;

    /**
     * The title.
     */
    protected string $title;

    /**
     * The description.
     */
    protected string $description;

    /**
     * Create a new instance of the template.
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Transform the template.
     */
    public function transform(): string
    {
        $transformed = collect();

        $body = $this->body;
        $body = Str::replaceFirst('{{', '', $body);
        $body = Str::replaceLast('}}', '', $body);

        $parts = array_map('trim', explode('|', $body));
        foreach ($parts as $part) {
            if ($this->isTitle($part)) {
                $this->title = trim(last(explode('=', $part)));
            } elseif ($this->isDescription($part)) {
                $this->description = trim(last(explode('=', $part)));
            } else {
                $transformed->push($part);
            }
        }

        if ($title = $this->composeTitle()) {
            $transformed->push($title);
        }

        return "{{{$transformed->implode('|')}}}";
    }

    /**
     * Check whether the given part is title or not.
     */
    protected function isTitle(string $part): bool
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^title(\s*)=(.+?)/', $part)
            || preg_match('/^название(\s*)=(.+?)/', $part);
    }

    /**
     * Check whether the given part is description or not.
     */
    protected function isDescription(string $part): bool
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^description(\s*)=(.+?)/', $part)
            || preg_match('/^описание(\s*)=(.+?)/', $part);
    }

    /**
     * Compose the title.
     */
    protected function composeTitle(): string|false
    {
        $composed = collect();

        if (empty($this->title) && empty($this->description)) {
            return false;
        }

        if (!empty($this->title)) {
            $composed->push(trim($this->title, '.'));
        }

        if (!empty($this->description)) {
            $composed->push($this->description);
        }

        return "title={$composed->implode(' - ')}";
    }
}
