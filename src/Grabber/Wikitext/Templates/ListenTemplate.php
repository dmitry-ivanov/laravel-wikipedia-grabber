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
     *
     * @var string
     */
    protected $body;

    /**
     * The title.
     *
     * @var string
     */
    protected $title;

    /**
     * The description.
     *
     * @var string
     */
    protected $description;

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
     * Transform the template.
     *
     * @return string
     */
    public function transform()
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
     *
     * @param string $part
     * @return bool
     */
    protected function isTitle(string $part)
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^title(\s*)=(.+?)/', $part)
            || preg_match('/^название(\s*)=(.+?)/', $part);
    }

    /**
     * Check whether the given part is description or not.
     *
     * @param string $part
     * @return bool
     */
    protected function isDescription(string $part)
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^description(\s*)=(.+?)/', $part)
            || preg_match('/^описание(\s*)=(.+?)/', $part);
    }

    /**
     * Compose the title.
     *
     * @return string|false
     */
    protected function composeTitle()
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
