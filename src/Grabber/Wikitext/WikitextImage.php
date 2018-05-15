<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class WikitextImage
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function getPosition()
    {
        $parts = explode('|', trim($this->body, '[]'));
        return in_array('left', $parts) ? 'left' : 'right';
    }
}
