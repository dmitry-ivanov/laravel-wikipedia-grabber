<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function section(Section $section)
    {
        $title = $section->getTitle();
        $body = nl2br($section->getBody());
        $tag = "h{$section->getHtmlLevel()}";

        return "<{$tag}>{$title}</{$tag}>\n<div>{$body}</div>\n\n";
    }
}
