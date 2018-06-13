<?php

namespace Illuminated\Wikipedia\Grabber\Component;

class Image
{
    protected $url;
    protected $mime;
    protected $width;
    protected $height;
    protected $position;
    protected $description;
    protected $originalUrl;

    public function __construct($url, $width, $height, $originalUrl, $position = 'right', $description = '', $mime = null)
    {
        $this->setUrl($url);
        $this->setMime($mime);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setPosition($position);
        $this->setDescription($description);
        $this->setOriginalUrl($originalUrl);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = (int) $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = (int) $height;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        if (!in_array($position, ['left', 'right'])) {
            $position = 'right';
        }

        $this->position = $position;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function setMime($mime)
    {
        $this->mime = $mime;
    }

    public function getAlt()
    {
        return htmlspecialchars($this->description, ENT_QUOTES);
    }

    public function isAudio()
    {
        $extensions = collect(['oga', 'mp3', 'wav'])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return ends_with($this->getOriginalUrl(), $extensions);
    }

    public function isVideo()
    {
        $extensions = collect(['ogv', 'mp4', 'webm'])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return ends_with($this->getOriginalUrl(), $extensions);
    }
}
