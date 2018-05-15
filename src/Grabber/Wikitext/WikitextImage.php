<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class WikitextImage extends Wikitext
{
    public function getPosition()
    {
        $parts = explode('|', trim($this->body, '[]'));
        return in_array('left', $parts) ? 'left' : 'right';
    }
}
