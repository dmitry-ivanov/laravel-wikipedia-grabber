<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

class PlainFormatter extends Formatter
{
    public function section(array $section)
    {
        $title = $section['title'];
        $body = nl2br($section['body']);
        $tag = $this->titleTag($section['level']);

        return "
            <{$tag}>{$title}</{$tag}>
            <div>{$body}</div>
        ";
    }
}
