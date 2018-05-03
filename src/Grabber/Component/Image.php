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
}
