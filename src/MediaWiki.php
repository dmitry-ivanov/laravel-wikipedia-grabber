<?php

namespace Illuminated\Wikipedia;

class MediaWiki extends Grabber
{
    /**
     * The URL.
     */
    protected string $url;

    /**
     * Create a new instance of MediaWiki grabber.
     */
    public function __construct(string $url)
    {
        $this->url = $url;

        parent::__construct();
    }

    /**
     * Get the base URI.
     */
    protected function baseUri(): string
    {
        return $this->url;
    }
}
