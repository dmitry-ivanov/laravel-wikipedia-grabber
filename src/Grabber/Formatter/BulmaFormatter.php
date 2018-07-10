<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

class BulmaFormatter extends IlluminatedFormatter
{
    // public function section(Section $section)
    // {
    //     $titleHtml = '';
    //     if ($title = $section->getTitle()) {
    //         $id = $this->sectionId($title);
    //         $htmlLevel = $section->getHtmlLevel();
    //         $class = "iwg-section-title title is-{$htmlLevel}" . ($section->hasGallery() ? ' has-gallery' : '');
    //         $titleHtml = "<h{$htmlLevel} id='{$id}' class='{$class}'>{$title}</h{$htmlLevel}>";
    //     }
    //
    //     $items = collect([
    //         $this->gallery($section),
    //         $this->images($section),
    //         $this->sectionBody($section),
    //     ]);
    //     $bodyHtml = $this->htmlBlock("<div class='iwg-section'>", $items, '</div>');
    //
    //     return $this->htmlBlock(null, collect([$titleHtml, $bodyHtml]), null);
    // }
}
