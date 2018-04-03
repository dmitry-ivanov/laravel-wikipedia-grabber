<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function tableOfContents(Collection $sections)
    {
        return 'TOC PLAIN';
    }

    public function section(Section $section)
    {
        $title = $section->getTitle();
        $body = nl2br($section->getBody());
        $tag = "h{$section->getHtmlLevel()}";

        $titleHtml = "<{$tag}>{$title}</{$tag}>\n";
        $bodyHtml = "<div>{$body}</div>\n\n";

        return "{$titleHtml}{$bodyHtml}";
    }
}
