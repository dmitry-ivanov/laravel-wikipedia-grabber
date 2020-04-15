<?php

namespace Illuminated\Wikipedia;

class MediaWiki extends Grabber
{
    /**
     * The URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new instance of MediaWiki grabber.
     *
     * @param string $url
     * @return void
     */
    public function __construct(string $url)
    {
        $this->url = $url;

        parent::__construct();
    }

    /**
     * Get the base URI.
     *
     * @return string
     */
    protected function baseUri()
    {
        return $this->url;
    }
}
