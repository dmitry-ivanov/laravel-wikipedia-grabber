<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $styles = collect([
            '.wiki-toc {padding:20px 0px}',
            '.wiki-toc-item {display:block}',
        ]);

        foreach ($this->getLevels() as $level) {
            $padding = ($level - 1) * 20;
            $styles->push(".wiki-toc-item.level-{$level} {padding-left:{$padding}px}");
        }

        $styles = $styles->implode("\n");

        return "<style>\n{$styles}\n</style>\n\n";
    }

    public function tableOfContents()
    {
        $items = collect();

        foreach ($this->sections as $section) {
            if ($section->isMain()) {
                continue;
            }

            $link = "<a href='#'>{$section->getTitle()}</a>";
            $items->push("<div class='wiki-toc-item level-{$section->getLevel()}'>{$link}</div>");
        }

        $items = $items->implode("\n");

        return "<div class='wiki-toc'>\n{$items}\n</div>\n\n";
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
