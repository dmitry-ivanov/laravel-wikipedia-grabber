<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $size = $this->toGallerySize(config('wikipedia-grabber.image_size'));
        $width = $size;
        $height = $size + 5;

        $styles = collect([
            '.wiki-toc {padding:20px 0px}',
            '.wiki-toc-item {display:block}',
            '.wiki-section-title.has-gallery {clear:both}',
            '.wiki-gallery {display:flex; flex-wrap:wrap; margin:0 -8px 16px -8px}',
            ".wiki-gallery .wiki-media {width:{$width}px; margin:8px; text-align:center}",
            ".wiki-gallery .wiki-media a {display:table-cell; width:{$width}px; height:{$height}px; vertical-align:middle}",
            '.wiki-media {color:#757575; padding:3px; margin-bottom:16px; box-shadow:0 4px 8px 0 #BDBDBD; transition:0.3s}',
            '.wiki-media:hover {box-shadow:0 8px 16px 0 #BDBDBD}',
            '.wiki-media.left {float:left; clear:left; margin-right:16px}',
            '.wiki-media.right {float:right; clear:right; margin-left:16px}',
            '.wiki-media-desc {padding:10px 16px; font-size:0.95rem; word-wrap:break-word}',
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

        $gallery = $this->gallery($section);
        $images = $this->images($section);
        $body = nl2br($section->getBody());

        $class = collect(['wiki-section-title']);
        if ($section->hasGallery()) {
            $class->push('has-gallery');
        }
        $class = $class->implode(' ');

        $titleHtml = "<{$tag} id='{$id}' class='{$class}'>{$title}</{$tag}>";
        $bodyHtml = "<div class='wiki-section'>\n{$gallery}{$images}{$body}\n</div>\n\n";
        if ($section->isEmpty()) {
            $bodyHtml = "\n";
        }

        return "{$titleHtml}\n{$bodyHtml}";
    }

    protected function gallery(Section $section)
    {
        if (!$section->hasGallery()) {
            return;
        }

        $gallery = $section->getGallery()->map(function (Image $image) {
            $url = $image->getUrl();
            $width = $this->toGallerySize($image->getWidth());
            $height = $this->toGallerySize($image->getHeight());
            $description = $image->getDescription();
            $originalUrl = $image->getOriginalUrl();

            $img = "<img src='{$url}' width='{$width}' height='{$height}' alt='{$description}' />";
            $link = "<a href='{$originalUrl}' target='_blank'>{$img}</a>";
            $desc = "<div class='wiki-media-desc'>{$description}</div>";
            if (empty($description)) {
                $desc = '';
            }

            return "<div class='wiki-media'>{$link}{$desc}</div>";
        })->implode("\n");

        return  "<div class='wiki-gallery'>\n{$gallery}\n</div>\n";
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

            $img = "<img src='{$url}' width='{$width}' height='{$height}' alt='{$description}' />";
            $link = "<a href='{$originalUrl}' target='_blank'>{$img}</a>";
            $desc = "<div class='wiki-media-desc'>{$description}</div>";
            if (empty($description)) {
                $desc = '';
            }

            return "<div class='wiki-media {$position}' style='width:{$width}px'>{$link}{$desc}</div>";
        })->implode("\n") . "\n";
    }
}
