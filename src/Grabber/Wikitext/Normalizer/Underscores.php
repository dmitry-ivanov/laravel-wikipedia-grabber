<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class Underscores
{
    public function normalize($wikitext)
    {
        return str_replace('_', ' ', $wikitext);
    }
}
