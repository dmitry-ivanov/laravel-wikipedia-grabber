<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;

class BulmaFormatter extends BasicFormatter
{
    /**
     * Compose the section's title `class` attribute.
     */
    protected function sectionTitleClass(Section $section): string
    {
        $htmlLevel = $section->getHtmlLevel();

        return "iwg-section-title title is-{$htmlLevel}" . ($section->hasGallery() ? ' has-gallery' : '');
    }
}
