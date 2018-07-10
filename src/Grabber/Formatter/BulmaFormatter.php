<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;

class BulmaFormatter extends IlluminatedFormatter
{
    protected function sectionTitleClass(Section $section)
    {
        $htmlLevel = $section->getHtmlLevel();
        return "iwg-section-title title is-{$htmlLevel}" . ($section->hasGallery() ? ' has-gallery' : '');
    }
}
