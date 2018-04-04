<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $styles = collect(['.wiki-toc {padding: 20px 0px;}']);

        foreach ($this->getLevels() as $level) {
            $padding = ($level - 1) * 20;
            $styles->push(".wiki-toc-item-level-{$level} {padding-left: {$padding}px;}");
        }

        $styles = $styles->implode("\n");

        return "<style>\n{$styles}\n</style>\n\n";
    }

    public function tableOfContents()
    {
        $toc = "<div class='wiki-toc'>\n";

        foreach ($this->sections as $section) {
            if ($section->isMain()) {
                continue;
            }

            $title = $section->getTitle();
            $level = $section->getLevel();

            $toc .= "<div class='wiki-toc-item-level-{$level}'><a href='#'>{$title}</a></div>\n";
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
