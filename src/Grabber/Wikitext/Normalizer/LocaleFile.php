<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

class LocaleFile
{
    public function normalize($wikitext)
    {
        return str_replace('Файл:', 'File:', $wikitext);
    }
}
