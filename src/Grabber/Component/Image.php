<?php

namespace Illuminated\Wikipedia\Grabber\Component;

class Image
{
    protected $url;
    protected $width;
    protected $height;
    protected $position;
    protected $description;
    protected $originalUrl;

    public function __construct($url, $width, $height, $originalUrl, $position = 'right', $description = '')
    {
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
        $this->position = $position;
        $this->description = $description;
        $this->originalUrl = $originalUrl;
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
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
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
}
