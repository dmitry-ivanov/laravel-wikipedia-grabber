<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class LocaleFile
{
    /**
     * Normalize locale files.
     *
     * @param string $wikitext
     * @return string
     */
    public function normalize(string $wikitext)
    {
        return str_replace('Файл:', 'File:', $wikitext);
    }
}
