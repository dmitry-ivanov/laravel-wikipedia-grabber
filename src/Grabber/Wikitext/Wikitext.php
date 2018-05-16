<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

/**
 * @see https://en.wikipedia.org/wiki/Help:Link
 * @see https://en.wikipedia.org/wiki/Help:Template
 * @see https://en.wikipedia.org/wiki/Template:Nowrap
 * @see https://www.mediawiki.org/wiki/Help:Formatting
 */
class Wikitext
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function removeFormatting($body = null)
    {
        $body = $body ?? $this->body;
        return preg_replace("/'{2,}/", '', $body);
    }

    public function removeLinks($body = null)
    {
        $body = $body ?? $this->body;

        if (!preg_match_all('/\[\[(.*?)\]\]/', $body, $matches, PREG_SET_ORDER)) {
            return $body;
        }

        foreach ($matches as $match) {
            $link = $match[0];
            $title = last(explode('|', $match[1]));
            $body = str_replace_first($link, $title, $body);
        }

        return $body;
    }

    public function removeTemplates($body = null)
    {
        $body = $body ?? $this->body;

        if (!preg_match_all('/\{\{(.*?)\}\}/', $body, $matches, PREG_SET_ORDER)) {
            return $body;
        }

        foreach ($matches as $match) {
            $template = $match[0];
            $parts = explode('|', $match[1]);
            $title = (count($parts) > 1) ? last($parts) : '';
            $body = str_replace_first($template, $title, $body);
        }

        return $body;
    }
}
