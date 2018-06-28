<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

/**
 * @see https://en.wikipedia.org/wiki/Template:Listen
 * @see https://ru.wikipedia.org/wiki/Шаблон:Listen
 */
class ListenTemplate
{
    protected $body;
    protected $title;
    protected $description;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function transform()
    {
        $transformed = collect();

        $body = $this->body;
        $body = str_replace_first('{{', '', $body);
        $body = str_replace_last('}}', '', $body);

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

    protected function isTitle($part)
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^title(\s*)=(.+?)/', $part) || preg_match('/^название(\s*)=(.+?)/', $part);
    }

    protected function isDescription($part)
    {
        $part = mb_strtolower($part, 'utf-8');

        return preg_match('/^description(\s*)=(.+?)/', $part) || preg_match('/^описание(\s*)=(.+?)/', $part);
    }

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
