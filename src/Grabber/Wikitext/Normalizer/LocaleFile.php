<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class LocaleFile
{
    /**
     * Normalize locale files.
     */
    public function normalize(string $wikitext): string
    {
        return str_replace('Файл:', 'File:', $wikitext);
    }
}
