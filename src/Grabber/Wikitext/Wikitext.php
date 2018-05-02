<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class Wikitext
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function sanitize()
    {
        return $this->removeLinks();
    }

    protected function removeLinks()
    {
        $sanitized = $this->body;

        if (!preg_match_all('/\[\[(.*?)\]\]/', $this->body, $matches, PREG_SET_ORDER)) {
            return $sanitized;
        }

        foreach ($matches as $match) {
            $link = $match[0];
            $title = last(explode('|', $match[1]));
            $sanitized = str_replace_first($link, $title, $sanitized);
        }

        return $sanitized;
    }
}
