<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $styles = collect([
            '.wiki-toc {padding:20px 0px}',
            '.wiki-toc-item {display:block}',
            '.wiki-media {color:#757575; padding:3px; margin-bottom:16px; box-shadow:0 4px 8px 0 #BDBDBD; transition:0.3s}',
            '.wiki-media:hover {box-shadow:0 8px 16px 0 #BDBDBD}',
            '.wiki-media.left {float:left; clear:left; margin-right:16px}',
            '.wiki-media.right {float:right; clear:right; margin-left:16px}',
            '.wiki-media .description {padding:10px 16px}',
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
        $items = $this->tocSections->map(function (Section $section) {
            $title = $section->getTitle();
            $link = "<a href='#{$this->sectionId($title)}'>{$title}</a>";
            return "<div class='wiki-toc-item level-{$section->getLevel()}'>{$link}</div>";
        });

        $items = $items->implode("\n");

        return "<div class='wiki-toc'>\n{$items}\n</div>\n\n";
    }

    public function section(Section $section)
    {
        $title = $section->getTitle();
        $id = $this->sectionId($title);
        $tag = "h{$section->getHtmlLevel()}";

        $images = $this->images($section);
        $body = nl2br($section->getBody());

        $titleHtml = "<{$tag} id='{$id}'>{$title}</{$tag}>";
        $bodyHtml = "<div>\n{$images}{$body}\n</div>\n\n";
        if (empty($images) && empty($body)) {
            $bodyHtml = "\n";
        }

        return "{$titleHtml}\n{$bodyHtml}";
    }

    protected function images(Section $section)
    {
        if (!$section->hasImages()) {
            return;
        }

        return $section->getImages()->map(function (Image $image) {
            $url = $image->getUrl();
            $width = $image->getWidth();
            $height = $image->getHeight();
            $position = $image->getPosition();
            $description = $image->getDescription();
            $originalUrl = $image->getOriginalUrl();

            $img = "<img src='{$url}' width='{$width}' height='{$height}' />";
            $link = "<a href='{$originalUrl}' target='_blank'>{$img}</a>";
            $desc = "<div class='description'>{$description}</div>";
            if (empty($description)) {
                $desc = '';
            }

            return "<div class='wiki-media {$position}' style='width:{$width}px;'>{$link}{$desc}</div>";
        })->implode("\n") . "\n";
    }
}
