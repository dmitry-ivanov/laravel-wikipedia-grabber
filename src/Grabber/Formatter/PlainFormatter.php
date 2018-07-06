<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;

class PlainFormatter extends Formatter
{
    public function style()
    {
        $styles = collect();

        if ($this->hasTableOfContents()) {
            $styles = $styles->merge(
                collect(['.iwg-toc {padding:20px 0px}'])->merge(
                    $this->tocLevels()->map(function ($level) {
                        $padding = ($level - 1) * 20;
                        return ".iwg-toc-item.level-{$level} {padding-left:{$padding}px}";
                    })
                )
            );
        }

        if ($this->hasGallery) {
            $galleryWidth = $this->toGallerySize(config('wikipedia-grabber.image_size'));
            $galleryHeight = $galleryWidth + 5;

            $styles = $styles->merge(collect([
                '.iwg-section-title.has-gallery {clear:both}',
                '.iwg-gallery {display:flex; flex-wrap:wrap; margin:0 -8px 16px -8px}',
                ".iwg-gallery .iwg-media {width:{$galleryWidth}px; margin:8px; text-align:center}",
                ".iwg-gallery .iwg-media a {display:table-cell; width:{$galleryWidth}px; height:{$galleryHeight}px; vertical-align:middle}",
            ]));
        }

        if ($this->hasMedia) {
            $styles = $styles->merge(collect([
                '.iwg-media {color:#757575; padding:3px; margin-bottom:16px; box-shadow:0 4px 8px 0 #BDBDBD; transition:0.3s}',
                '.iwg-media:hover {box-shadow:0 8px 16px 0 #BDBDBD}',
                '.iwg-media.left {float:left; clear:left; margin-right:16px}',
                '.iwg-media.right {float:right; clear:right; margin-left:16px}',
                '.iwg-media.audio, .iwg-media.video {width:275px; padding:5px 5px 3px 5px}',
                '.iwg-media audio, .iwg-media video {width:100%}',
                '.iwg-media-desc {padding:10px 16px; font-size:0.95rem; word-wrap:break-word}',
            ]));
        }

        return $this->htmlBlock('<style>', $styles, '</style>');
    }

    public function tableOfContents()
    {
        $items = $this->tocSections->map(function (Section $section) {
            $title = $section->getTitle();
            $link = "<a href='#{$this->sectionId($title)}'>{$title}</a>";
            return "<div class='iwg-toc-item level-{$section->getLevel()}'>{$link}</div>";
        });

        return $this->htmlBlock("<div class='iwg-toc'>", $items, '</div>');
    }

    public function section(Section $section)
    {
        $titleHtml = '';
        if ($title = $section->getTitle()) {
            $id = $this->sectionId($title);
            $tag = "h{$section->getHtmlLevel()}";
            $class = 'iwg-section-title' . ($section->hasGallery() ? ' has-gallery' : '');
            $titleHtml = "<{$tag} id='{$id}' class='{$class}'>{$title}</{$tag}>";
        }

        $items = collect([
            $this->gallery($section),
            $this->images($section),
            $this->sectionBody($section),
        ]);
        $bodyHtml = $this->htmlBlock("<div class='iwg-section'>", $items, '</div>');

        return $this->htmlBlock(null, collect([$titleHtml, $bodyHtml]), null);
    }

    protected function gallery(Section $section)
    {
        if (!$section->hasGallery()) {
            return;
        }

        $gallery = $section->getGallery()->map(function (Image $image) {
            return $this->media($image, true);
        });

        return $this->htmlBlock("<div class='iwg-gallery'>", $gallery, '</div>');
    }

    protected function images(Section $section)
    {
        if (!$section->hasImages()) {
            return;
        }

        $images = $section->getImages()->map(function (Image $image) {
            return $this->media($image);
        });

        return $this->htmlBlock(null, $images, null);
    }

    protected function media(Image $image, $isGallery = false)
    {
        if ($image->isAudio()) {
            return $this->audio($image, $isGallery);
        }

        if ($image->isVideo()) {
            return $this->video($image, $isGallery);
        }

        return $this->image($image, $isGallery);
    }

    protected function image(Image $image, $isGallery = false)
    {
        $url = $image->getUrl();
        $alt = $image->getAlt();
        $width = $image->getWidth();
        $height = $image->getHeight();
        $position = $image->getPosition();
        $description = $image->getDescription();
        $originalUrl = $image->getOriginalUrl();

        if ($isGallery) {
            $width = $this->toGallerySize($width);
            $height = $this->toGallerySize($height);
        }

        $img = "<img src='{$url}' width='{$width}' height='{$height}' alt='{$alt}' />";
        $link = "<a href='{$originalUrl}' target='_blank'>{$img}</a>";
        $desc = !empty($description) ? "<div class='iwg-media-desc'>{$description}</div>" : '';

        if ($isGallery) {
            return "<div class='iwg-media'>{$link}{$desc}</div>";
        }

        return "<div class='iwg-media {$position}' style='width:{$width}px'>{$link}{$desc}</div>";
    }

    protected function audio(Image $image, $isGallery = false)
    {
        $mime = $image->getMime();
        $position = $image->getPosition();
        $description = $image->getDescription();
        $originalUrl = $image->getOriginalUrl();

        $source = collect(["<source src='{$originalUrl}' type='{$mime}'>"]);
        if ($mp3 = $image->getTranscodedMp3Url()) {
            $source->push("<source src='{$mp3}' type='audio/mpeg'>");
        }

        $audio = "<audio controls>{$source->implode('')}</audio>";
        $desc = !empty($description) ? "<div class='iwg-media-desc'>{$description}</div>" : '';

        if ($isGallery) {
            return "<div class='iwg-media audio'>{$audio}{$desc}</div>";
        }

        return "<div class='iwg-media audio {$position}'>{$audio}{$desc}</div>";
    }

    protected function video(Image $image, $isGallery = false)
    {
        $url = $image->getUrl();
        $mime = $image->getMime();
        $position = $image->getPosition();
        $description = $image->getDescription();
        $originalUrl = $image->getOriginalUrl();

        $source = collect(["<source src='{$originalUrl}' type='{$mime}'>"]);
        if ($transcoded = $image->getTranscodedWebmUrls()) {
            $transcoded->each(function ($webm) use ($source) {
                $source->push("<source src='{$webm}' type='video/webm'>");
            });
        }

        $video = "<video poster='{$url}' controls>{$source->implode('')}</video>";
        $desc = !empty($description) ? "<div class='iwg-media-desc'>{$description}</div>" : '';

        if ($isGallery) {
            return "<div class='iwg-media video'>{$video}{$desc}</div>";
        }

        return "<div class='iwg-media video {$position}'>{$video}{$desc}</div>";
    }
}
