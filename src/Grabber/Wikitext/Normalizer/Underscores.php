<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class Underscores
{
    /**
     * Normalize underscores.
     *
     * @param string $wikitext
     * @return string
     */
    public function normalize(string $wikitext)
    {
        return str_replace('_', ' ', $wikitext);
    }
}
