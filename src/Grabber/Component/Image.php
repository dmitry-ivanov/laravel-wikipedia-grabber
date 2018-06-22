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
        $this->mime = mb_strtolower($mime, 'utf-8');
    }

    public function getAlt()
    {
        return htmlspecialchars($this->getDescription(), ENT_QUOTES);
    }

    public function isAudio()
    {
        $originalUrl = $this->getOriginalUrl();

        if (ends_with($originalUrl, ['oga', 'mp3', 'wav'])) {
            return true;
        }

        if (ends_with($originalUrl, 'ogg')) {
            return !str_contains($this->getMime(), 'video');
        }

        return false;
    }

    public function isVideo()
    {
        $originalUrl = $this->getOriginalUrl();

        if (ends_with($originalUrl, ['ogv', 'mp4', 'webm'])) {
            return true;
        }

        if (ends_with($originalUrl, 'ogg')) {
            return str_contains($this->getMime(), 'video');
        }

        return false;
    }

    public function getTranscodedMp3Url()
    {
        $originalUrl = $this->getOriginalUrl();

        if (!$this->isAudio() || ends_with($originalUrl, 'mp3')) {
            return false;
        }

        $start = preg_quote('://upload.wikimedia.org/wikipedia', '/');
        if (!preg_match("/(.*?{$start}\/.*?)\/(.*)/", $originalUrl, $matches)) {
            return false;
        }

        $name = basename($originalUrl);

        return "{$matches[1]}/transcoded/{$matches[2]}/{$name}.mp3";
    }

    public function getTranscodedWebmUrls()
    {
        $originalUrl = $this->getOriginalUrl();

        if (!$this->isVideo()) {
            return false;
        }

        $start = preg_quote('://upload.wikimedia.org/wikipedia', '/');
        if (!preg_match("/(.*?{$start}\/.*?)\/(.*)/", $originalUrl, $matches)) {
            return false;
        }

        $name = basename($originalUrl);

        return collect(['720p', '480p', '360p', '240p', '160p'])->map(function ($quality) use ($matches, $name) {
            return "{$matches[1]}/transcoded/{$matches[2]}/{$name}.{$quality}.webm";
        });
    }
}
