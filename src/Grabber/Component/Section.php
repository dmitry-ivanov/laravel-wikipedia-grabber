<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class Section
{
    /**
     * The title.
     *
     * @var string
     */
    protected $title;

    /**
     * The body.
     *
     * @var string
     */
    protected $body;

    /**
     * The level.
     *
     * @var int
     */
    protected $level;

    /**
     * The images collection.
     *
     * @var \Illuminate\Support\Collection|null
     */
    protected $images;

    /**
     * The gallery collection.
     *
     * @var \Illuminate\Support\Collection|null
     */
    protected $gallery;

    /**
     * Create a new instance of the Section.
     *
     * @param string $title
     * @param string $body
     * @param int $level
     * @param \Illuminate\Support\Collection|null $images
     * @return void
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
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $title = $this->removeSpecialChars($title);
        $title = (new Wikitext($title))->plain();

        $this->title = trim($title);
    }

    /**
     * Get the body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body.
     *
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->body = trim($body);
    }

    /**
     * Get the level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set the level.
     *
     * @param int $level
     * @return void
     */
    public function setLevel(int $level)
    {
        if ($level < 1) {
            $level = 1;
        }

        $this->level = $level;
    }

    /**
     * Get the images.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set the images.
     *
     * @param \Illuminate\Support\Collection|null $images
     * @return void
     */
    public function setImages(Collection $images = null)
    {
        $this->images = $images ?? collect();
    }

    /**
     * Get the gallery.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set the gallery.
     *
     * @param \Illuminate\Support\Collection|null $gallery
     * @return void
     */
    public function setGallery(Collection $gallery = null)
    {
        $this->gallery = $gallery ?? collect();
    }

    /**
     * Check whether the section is main or not.
     *
     * @return bool
     */
    public function isMain()
    {
        return $this->level == 1;
    }

    /**
     * Check whether the section is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->body)
            && !$this->hasImages()
            && !$this->hasGallery();
    }

    /**
     * Check whether the section has images or not.
     *
     * @return bool
     */
    public function hasImages()
    {
        return $this->images->isNotEmpty();
    }

    /**
     * Add images to section.
     *
     * @param \Illuminate\Support\Collection $images
     * @return void
     */
    public function addImages(Collection $images)
    {
        $this->images = $this->images->merge($images);
    }

    /**
     * Check whether the section has gallery or not.
     *
     * @return bool
     */
    public function hasGallery()
    {
        return $this->gallery->isNotEmpty();
    }

    /**
     * Get HTML level of the section.
     *
     * @return int
     */
    public function getHtmlLevel()
    {
        // We have only h1..h6 HTML tags.
        if ($this->level > 6) {
            return 6;
        }

        return $this->level;
    }

    /**
     * Remove special characters from the given string.
     *
     * @param string $string
     * @return string
     */
    protected function removeSpecialChars(string $string)
    {
        $string = str_replace(chr(194) . chr(160), ' ', $string);
        $string = str_replace(chr(226) . chr(128) . chr(137), ' ', $string);

        return $string;
    }
}
