<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class Underscores
{
    /**
     * Normalize underscores.
     */
    public function normalize(string $wikitext): string
    {
        return str_replace('_', ' ', $wikitext);
    }
}
