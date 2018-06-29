<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class Section
{
    protected $title;
    protected $body;
    protected $level;
    protected $images;
    protected $gallery;

    public function __construct($title, $body, $level, Collection $images = null)
    {
        $this->setTitle($title);
        $this->setBody($body);
        $this->setLevel($level);
        $this->setImages($images);
        $this->setGallery();
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $title = $this->removeSpecialChars($title);
        $title = (new Wikitext($title))->plain();
        $title = preg_replace('/\s{2,}/', ' ', trim($title));

        $this->title = $title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = trim($body);
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $level = (int) $level;

        if ($level < 1) {
            $level = 1;
        }

        $this->level = $level;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages(Collection $images = null)
    {
        $this->images = $images ?? collect();
    }

    public function getGallery()
    {
        return $this->gallery;
    }

    public function setGallery(Collection $gallery = null)
    {
        $this->gallery = $gallery ?? collect();
    }

    public function isMain()
    {
        return ($this->level == 1);
    }

    public function isEmpty()
    {
        return empty($this->body) && !$this->hasImages() && !$this->hasGallery();
    }

    public function hasImages()
    {
        return $this->images->isNotEmpty();
    }

    public function addImages(Collection $images)
    {
        $this->images = $this->images->merge($images);
    }

    public function hasGallery()
    {
        return $this->gallery->isNotEmpty();
    }

    public function getHtmlLevel()
    {
        // We have only h1..h6 html tags.
        if ($this->level > 6) {
            return 6;
        }

        return $this->level;
    }

    protected function removeSpecialChars($string)
    {
        $string = str_replace(chr(194) . chr(160), ' ', $string);
        $string = str_replace(chr(226) . chr(128) . chr(137), ' ', $string);

        return $string;
    }
}
