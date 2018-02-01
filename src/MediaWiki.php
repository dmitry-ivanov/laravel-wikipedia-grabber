<?php

namespace Illuminated\Wikipedia;

class MediaWiki extends Grabber
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
}
