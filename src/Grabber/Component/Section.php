<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class Section
{
    /**
     * The title.
     */
    protected string $title;

    /**
     * The body.
     */
    protected string $body;

    /**
     * The level.
     */
    protected int $level;

    /**
     * The images collection.
     */
    protected ?Collection $images;

    /**
     * The gallery collection.
     */
    protected ?Collection $gallery;

    /**
     * Create a new instance of the Section.
     */
    public function __construct(string $title, string $body, int $level, Collection $images = null)
    {
        $this->setTitle($title);
        $this->setBody($body);
        $this->setLevel($level);
        $this->setImages($images);
        $this->setGallery(null);
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title.
     */
    public function setTitle(string $title): void
    {
        $title = $this->removeSpecialChars($title);
        $title = (new Wikitext($title))->plain();

        $this->title = trim($title);
    }

    /**
     * Get the body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Set the body.
     */
    public function setBody(string $body): void
    {
        $this->body = trim($body);
    }

    /**
     * Get the level.
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Set the level.
     */
    public function setLevel(int $level): void
    {
        if ($level < 1) {
            $level = 1;
        }

        $this->level = $level;
    }

    /**
     * Get the images.
     */
    public function getImages(): Collection|null
    {
        return $this->images;
    }

    /**
     * Set the images.
     */
    public function setImages(Collection $images = null): void
    {
        $this->images = $images ?? collect();
    }

    /**
     * Get the gallery.
     */
    public function getGallery(): Collection|null
    {
        return $this->gallery;
    }

    /**
     * Set the gallery.
     */
    public function setGallery(Collection $gallery = null): void
    {
        $this->gallery = $gallery ?? collect();
    }

    /**
     * Check whether the section is main or not.
     */
    public function isMain(): bool
    {
        return $this->level == 1;
    }

    /**
     * Check whether the section is empty or not.
     */
    public function isEmpty(): bool
    {
        return empty($this->body)
            && !$this->hasImages()
            && !$this->hasGallery();
    }

    /**
     * Check whether the section has images or not.
     */
    public function hasImages(): bool
    {
        return $this->images->isNotEmpty();
    }

    /**
     * Add images to section.
     */
    public function addImages(Collection $images): void
    {
        $this->images = $this->images->merge($images);
    }

    /**
     * Check whether the section has gallery or not.
     */
    public function hasGallery(): bool
    {
        return $this->gallery->isNotEmpty();
    }

    /**
     * Get HTML level of the section.
     */
    public function getHtmlLevel(): int
    {
        // We have only h1..h6 HTML tags.
        if ($this->level > 6) {
            return 6;
        }

        return $this->level;
    }

    /**
     * Remove special characters from the given string.
     */
    protected function removeSpecialChars(string $string): string
    {
        $string = str_replace(chr(194) . chr(160), ' ', $string);
        $string = str_replace(chr(226) . chr(128) . chr(137), ' ', $string);

        return $string;
    }
}
