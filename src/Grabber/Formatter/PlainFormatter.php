<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $style = "<style>\n";
        $style .= ".wiki-toc {}";
        $style .= "</style>\n\n";

        return $style;
    }

    public function tableOfContents(Collection $sections)
    {
        $toc = "<div style='padding: 20px 0px;'>\n";

        foreach ($sections as $section) {
            if ($section->isMain()) {
                continue;
            }

            $title = $section->getTitle();
            $level = $section->getLevel();
            $padding = ($level - 1) * 20;

            $toc .= "<div style='padding-left: {$padding}px;'><a href='#'>{$title}</a></div>\n";
        }

        $toc .= "</div>\n\n";

        return $toc;
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
