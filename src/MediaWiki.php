<?php

namespace Illuminated\Wikipedia;

class MediaWiki extends Grabber
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;

        parent::__construct();
    }

    protected function baseUri()
    {
        return $this->url;
    }
}
