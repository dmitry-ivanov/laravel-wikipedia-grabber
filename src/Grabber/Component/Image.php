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
        $this->setUrl($url);
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
}
