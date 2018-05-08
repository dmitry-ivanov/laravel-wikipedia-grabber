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

        $styles = $styles->merge(
            collect($this->getLevels())->map(function ($level) {
                $padding = ($level - 1) * 20;
                return ".wiki-toc-item.level-{$level} {padding-left:{$padding}px}";
            })
        );

        $styles = $styles->implode("\n");

        return "<style>\n{$styles}\n</style>\n\n";
    }

    public function tableOfContents()
    {
        $items = $this->sections->map(function (Section $section) {
            if ($section->isMain()) {
                return null;
            }

            $title = $section->getTitle();
            $link = "<a href='#{$this->sectionId($title)}'>{$title}</a>";

            return "<div class='wiki-toc-item level-{$section->getLevel()}'>{$link}</div>";
        })->filter();

        $items = $items->implode("\n");

        return "<div class='wiki-toc'>\n{$items}\n</div>\n\n";
    }

    public function section(Section $section)
    {
        $title = $section->getTitle();
        $body = nl2br($section->getBody());
        $tag = "h{$section->getHtmlLevel()}";

        $titleHtml = "<{$tag} id='{$this->sectionId($title)}'>{$title}</{$tag}>\n";
        $bodyHtml = "<div>{$body}</div>\n\n";

        return "{$titleHtml}{$bodyHtml}";
    }
}
