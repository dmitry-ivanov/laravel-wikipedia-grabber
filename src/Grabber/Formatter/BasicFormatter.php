<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;

class BasicFormatter extends Formatter
{
    /**
     * Indicates whether the given sections have media or not.
     */
    protected bool $hasMedia;

    /**
     * Indicates whether the given sections have gallery or not.
     */
    protected bool $hasGallery;

    /**
     * Sections which should be included to the table of contents.
     */
    protected Collection $tocSections;

    /**
     * Create a new instance of the BasicFormatter.
     */
    public function __construct(Collection $sections)
    {
        $this->hasMedia = (bool) $sections->first(function (Section $section) {
            return $section->hasImages();
        });

        $this->hasGallery = (bool) $sections->first(function (Section $section) {
            return $section->hasGallery();
        });

        $this->tocSections = $sections->filter(function (Section $section) {
            return !$section->isMain();
        });
    }

    /**
     * Compose the style.
     */
    public function style(): string
    {
        $styles = collect(['.iwg-section-title, .iwg-section {margin-bottom:1.5rem}']);

        if ($this->hasTableOfContents()) {
            $styles->push('.iwg-toc {margin-bottom:1.5rem}');
            $styles = $styles->merge(
                $this->tocLevels()->map(function ($level) {
                    $margin = ($level - 2) * 1.5;
                    return ".iwg-toc-item.level-{$level} {margin-left:{$margin}rem}";
                })
            );
        }

        if ($this->hasGallery) {
            $galleryWidth = $this->toGallerySize(config('wikipedia-grabber.image_size'));
            $galleryHeight = $galleryWidth + 5;

            $styles = $styles->merge(collect([
                '.iwg-section-title.has-gallery {clear:both}',
                '.iwg-gallery {display:flex; flex-wrap:wrap; margin:0 -.5rem 1rem -.5rem}',
                ".iwg-gallery .iwg-media {width:{$galleryWidth}px; margin:.5rem; text-align:center}",
                ".iwg-gallery .iwg-media a {display:table-cell; width:{$galleryWidth}px; height:{$galleryHeight}px; vertical-align:middle}",
            ]));
        }

        if ($this->hasMedia) {
            $styles = $styles->merge(collect([
                '.iwg-media {color:#757575; margin-bottom:1rem; padding:3px; box-sizing:initial; box-shadow:0 .25rem .5rem 0 #BDBDBD; transition:.3s}',
                '.iwg-media:hover {box-shadow:0 .5rem 1rem 0 #BDBDBD}',
                '.iwg-media.left {float:left; clear:left; margin-right:1rem}',
                '.iwg-media.right {float:right; clear:right; margin-left:1rem}',
                '.iwg-media.audio, .iwg-media.video {width:275px; padding:5px}',
                '.iwg-media audio, .iwg-media video {width:100%}',
                '.iwg-media-desc {padding:.625rem 1rem; font-size:0.95em; word-wrap:break-word}',
            ]));
        }

        return $this->htmlBlock('<style>', $styles, '</style>');
    }

    /**
     * Compose the table of contents.
     */
    public function tableOfContents(): string
    {
        $items = $this->tocSections->map(function (Section $section) {
            $title = $section->getTitle();
            $link = "<a href='#{$this->sectionId($title)}'>{$title}</a>";
            return "<div class='iwg-toc-item level-{$section->getLevel()}'>{$link}</div>";
        });

        return $this->htmlBlock("<div class='iwg-toc'>", $items, '</div>');
    }

    /**
     * Compose the section.
     */
    public function section(Section $section): string
    {
        $titleHtml = '';
        if ($title = $section->getTitle()) {
            $id = $this->sectionId($title);
            $tag = "h{$section->getHtmlLevel()}";
            $class = $this->sectionTitleClass($section);
            $titleHtml = "<{$tag} id='{$id}' class='{$class}'>{$title}</{$tag}>";
        }

        $items = collect([
            $this->gallery($section),
            $this->images($section),
            $this->sectionBody($section),
        ]);
        $bodyHtml = $this->htmlBlock("<div class='iwg-section'>", $items, '</div>');

        return $this->htmlBlock('', collect([$titleHtml, $bodyHtml]), '');
    }

    /**
     * Compose the gallery for the given section.
     */
    protected function gallery(Section $section): string
    {
        if (!$section->hasGallery()) {
            return '';
        }

        $gallery = $section->getGallery()->map(function (Image $image) {
            return $this->media($image, true);
        });

        return $this->htmlBlock("<div class='iwg-gallery'>", $gallery, '</div>');
    }

    /**
     * Compose the images for the given section.
     */
    protected function images(Section $section): string
    {
        if (!$section->hasImages()) {
            return '';
        }

        $images = $section->getImages()->map(function (Image $image) {
            return $this->media($image);
        });

        return $this->htmlBlock('', $images, '');
    }

    /**
     * Compose the media.
     */
    protected function media(Image $image, bool $isGallery = false): string
    {
        if ($image->isAudio()) {
            return $this->audio($image, $isGallery);
        }

        if ($image->isVideo()) {
            return $this->video($image, $isGallery);
        }

        return $this->image($image, $isGallery);
    }

    /**
     * Compose the image.
     */
    protected function image(Image $image, bool $isGallery = false): string
    {
        $url = $image->getUrl();
        $width = $image->getWidth();
        $height = $image->getHeight();
        $alt = $image->getAlt();
        $originalUrl = $image->getOriginalUrl();
        $description = $image->getDescription();
        $position = $image->getPosition();

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

    /**
     * Compose an audio.
     */
    protected function audio(Image $image, bool $isGallery = false): string
    {
        $originalUrl = $image->getOriginalUrl();
        $mime = $image->getMime();
        $description = $image->getDescription();
        $position = $image->getPosition();

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

    /**
     * Compose the video.
     */
    protected function video(Image $image, bool $isGallery = false): string
    {
        $originalUrl = $image->getOriginalUrl();
        $mime = $image->getMime();
        $url = $image->getUrl();
        $description = $image->getDescription();
        $position = $image->getPosition();

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

    /**
     * Check whether there is a table of contents.
     */
    protected function hasTableOfContents(): bool
    {
        return $this->tocSections->isNotEmpty();
    }

    /**
     * Compose the TOC levels.
     */
    protected function tocLevels(): Collection
    {
        return $this->tocSections->map(function (Section $section) {
            return $section->getLevel();
        })->unique()->sort();
    }

    /**
     * Compose the section's title `class` attribute.
     */
    protected function sectionTitleClass(Section $section): string
    {
        return 'iwg-section-title' . ($section->hasGallery() ? ' has-gallery' : '');
    }

    /**
     * Compose the section ID.
     */
    protected function sectionId(string $title): string
    {
        return Str::slug($title);
    }

    /**
     * Compose the section body.
     */
    protected function sectionBody(Section $section): string
    {
        return preg_replace('/(\s*<br.*?>\s*){3,}/m', '$1$1', nl2br($section->getBody()));
    }

    /**
     * Convert the image size to the gallery size.
     */
    protected function toGallerySize(int $size): int
    {
        return (int) ($size / 1.35);
    }

    /**
     * Compose the HTML block.
     */
    protected function htmlBlock(string $open, Collection $items, string $close): string
    {
        $items = collect(array_map('trim', $items->toArray()))->filter();
        if ($items->isEmpty()) {
            return '';
        }

        $open .= !empty($open) ? "\n" : '';
        $close .= !empty($close) ? "\n" : '';

        return "{$open}{$items->implode("\n")}\n{$close}\n";
    }
}
