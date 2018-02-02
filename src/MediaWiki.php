<?php

namespace Illuminated\Wikipedia;

class MediaWiki extends Grabber
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;

        parent::__construct();
    }

    protected function getBaseUri()
    {
        return $this->url;
    }
}
