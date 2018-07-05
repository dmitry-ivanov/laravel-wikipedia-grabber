<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;

class BulmaFormatter extends Formatter
{
    public function style()
    {
        // $styles = collect();
        //
        // if ($this->hasTableOfContents()) {
        //     $styles = $styles->merge(
        //         collect([
        //             '.wiki-toc {padding:20px 0px}',
        //             '.iwg-toc-item {display:block}',
        //         ])->merge(
        //             $this->getLevels()->map(function ($level) {
        //                 $padding = ($level - 1) * 20;
        //                 return ".iwg-toc-item.level-{$level} {padding-left:{$padding}px}";
        //             })
        //         )
        //     );
        // }
        //
        // if ($this->hasGallery) {
        //     $galleryWidth = $this->toGallerySize(config('wikipedia-grabber.image_size'));
        //     $galleryHeight = $galleryWidth + 5;
        //
        //     $styles = $styles->merge(collect([
        //         '.wiki-section-title.has-gallery {clear:both}',
        //         '.wiki-gallery {display:flex; flex-wrap:wrap; margin:0 -8px 16px -8px}',
        //         ".wiki-gallery .wiki-media {width:{$galleryWidth}px; margin:8px; text-align:center}",
        //         ".wiki-gallery .wiki-media a {display:table-cell; width:{$galleryWidth}px; height:{$galleryHeight}px; vertical-align:middle}",
        //     ]));
        // }
        //
        // if ($this->hasMedia) {
        //     $styles = $styles->merge(collect([
        //         '.wiki-media {color:#757575; padding:3px; margin-bottom:16px; box-shadow:0 4px 8px 0 #BDBDBD; transition:0.3s}',
        //         '.wiki-media:hover {box-shadow:0 8px 16px 0 #BDBDBD}',
        //         '.wiki-media.left {float:left; clear:left; margin-right:16px}',
        //         '.wiki-media.right {float:right; clear:right; margin-left:16px}',
        //         '.wiki-media-desc {padding:10px 16px; font-size:0.95rem; word-wrap:break-word}',
        //         '.wiki-media.audio, .wiki-media.video {width:275px; padding:5px 5px 3px 5px}',
        //         '.wiki-media audio, .wiki-media video {width:100%}',
        //     ]));
        // }
        //
        // if ($styles->isEmpty()) {
        //     return;
        // }
        //
        // $styles = $styles->implode("\n");
        //
        // return "<style>\n{$styles}\n</style>\n\n";
    }

    public function tableOfContents()
    {
        // if (!$this->hasTableOfContents()) {
        //     return;
        // }
        //
        // $items = $this->tocSections->map(function (Section $section) {
        //     $title = $section->getTitle();
        //     $link = "<a href='#{$this->sectionId($title)}'>{$title}</a>";
        //     return "<div class='iwg-toc-item level-{$section->getLevel()}'>{$link}</div>";
        // });
        //
        // $items = $items->implode("\n");
        //
        // return "<div class='wiki-toc'>\n{$items}\n</div>\n\n";
    }

    public function section(Section $section)
    {
        $title = $section->getTitle();
        $id = $this->sectionId($title);
        $htmlLevel = $section->getHtmlLevel();
        $tag = "h{$htmlLevel}";

        // $gallery = $this->gallery($section);
        // $images = $this->images($section);
        $gallery = ''; /////////////////////////////////////////////////////////////////////////////////////////////////
        $images = ''; //////////////////////////////////////////////////////////////////////////////////////////////////
        $body = $this->sectionBody($section);

        $class = collect(['wiki-section-title', 'title', "is-{$htmlLevel}"]);
        // if ($section->hasGallery()) {
        //     $class->push('has-gallery');
        // }
        $class = $class->implode(' ');

        $titleHtml = !empty($title) ? "<{$tag} id='{$id}' class='{$class}'>{$title}</{$tag}>\n" : '';
        $bodyHtml = "<div class='wiki-section content'>\n{$gallery}{$images}{$body}\n</div>\n\n";
        if ($section->isEmpty()) {
            $bodyHtml = "\n";
        }

        return "{$titleHtml}{$bodyHtml}";
    }

    protected function gallery(Section $section)
    {
        // if (!$section->hasGallery()) {
        //     return;
        // }
        //
        // $gallery = $section->getGallery()->map(function (Image $image) {
        //     return $this->media($image, true);
        // })->implode("\n");
        //
        // return  "<div class='wiki-gallery'>\n{$gallery}\n</div>\n";
    }

    protected function images(Section $section)
    {
        // if (!$section->hasImages()) {
        //     return;
        // }
        //
        // return $section->getImages()->map(function (Image $image) {
        //     return $this->media($image);
        // })->implode("\n") . "\n";
    }

    protected function media(Image $image, $isGallery = false)
    {
        // if ($image->isAudio()) {
        //     return $this->audio($image, $isGallery);
        // }
        //
        // if ($image->isVideo()) {
        //     return $this->video($image, $isGallery);
        // }
        //
        // return $this->image($image, $isGallery);
    }

    protected function image(Image $image, $isGallery = false)
    {
        // $url = $image->getUrl();
        // $alt = $image->getAlt();
        // $width = $image->getWidth();
        // $height = $image->getHeight();
        // $position = $image->getPosition();
        // $description = $image->getDescription();
        // $originalUrl = $image->getOriginalUrl();
        //
        // if ($isGallery) {
        //     $width = $this->toGallerySize($width);
        //     $height = $this->toGallerySize($height);
        // }
        //
        // $img = "<img src='{$url}' width='{$width}' height='{$height}' alt='{$alt}' />";
        // $link = "<a href='{$originalUrl}' target='_blank'>{$img}</a>";
        // $desc = !empty($description) ? "<div class='wiki-media-desc'>{$description}</div>" : '';
        //
        // if ($isGallery) {
        //     return "<div class='wiki-media'>{$link}{$desc}</div>";
        // }
        //
        // return "<div class='wiki-media {$position}' style='width:{$width}px'>{$link}{$desc}</div>";
    }

    protected function audio(Image $image, $isGallery = false)
    {
        // $mime = $image->getMime();
        // $position = $image->getPosition();
        // $description = $image->getDescription();
        // $originalUrl = $image->getOriginalUrl();
        //
        // $source = collect(["<source src='{$originalUrl}' type='{$mime}'>"]);
        // if ($mp3 = $image->getTranscodedMp3Url()) {
        //     $source->push("<source src='{$mp3}' type='audio/mpeg'>");
        // }
        //
        // $audio = "<audio controls>{$source->implode('')}</audio>";
        // $desc = !empty($description) ? "<div class='wiki-media-desc'>{$description}</div>" : '';
        //
        // if ($isGallery) {
        //     return "<div class='wiki-media audio'>{$audio}{$desc}</div>";
        // }
        //
        // return "<div class='wiki-media audio {$position}'>{$audio}{$desc}</div>";
    }

    protected function video(Image $image, $isGallery = false)
    {
        // $url = $image->getUrl();
        // $mime = $image->getMime();
        // $position = $image->getPosition();
        // $description = $image->getDescription();
        // $originalUrl = $image->getOriginalUrl();
        //
        // $source = collect(["<source src='{$originalUrl}' type='{$mime}'>"]);
        // if ($transcoded = $image->getTranscodedWebmUrls()) {
        //     $transcoded->each(function ($webm) use ($source) {
        //         $source->push("<source src='{$webm}' type='video/webm'>");
        //     });
        // }
        //
        // $video = "<video poster='{$url}' controls>{$source->implode('')}</video>";
        // $desc = !empty($description) ? "<div class='wiki-media-desc'>{$description}</div>" : '';
        //
        // if ($isGallery) {
        //     return "<div class='wiki-media video'>{$video}{$desc}</div>";
        // }
        //
        // return "<div class='wiki-media video {$position}'>{$video}{$desc}</div>";
    }
}
