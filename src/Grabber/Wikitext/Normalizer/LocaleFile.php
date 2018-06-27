<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Component\Section;

class LocaleFile
{
    public function normalize(Section $section)
    {
        return str_replace('Файл:', 'File:', $section->getBody());
    }
}
