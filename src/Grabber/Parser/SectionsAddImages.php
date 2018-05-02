<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class SectionsAddImages
{
    protected $sections;
    protected $hasImages = false;
    protected $images;
    protected $mainImage;
    protected $wikitext;
    protected $wikitextSections;

    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        $this->sections = $sections;

        if (!empty($imagesResponseData)) {
            $this->hasImages = true;
            $this->images = $imagesResponseData['images'];
            $this->mainImage = $imagesResponseData['main_image'];
            $this->wikitext = $imagesResponseData['wikitext'];
        }
    }

    public function filter()
    {
        if (!$this->hasImages) {
            return $this->sections;
        }

        foreach ($this->sections as $section) {
            $wikitextSection = $this->getWikitextSection($section);
            if (empty($wikitextSection)) {
                continue;
            }

            dd($section, $wikitextSection);
        }

        dd('filter method');

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }

    protected function getWikitextSection(Section $section)
    {
        $wikitextSections = $this->getWikitextSections();

        return $wikitextSections->first(function (Section $wikiSection) use ($section) {
            return ($wikiSection->getTitle() == $section->getTitle())
                && ($wikiSection->getLevel() == $section->getLevel());
        });
    }

    protected function getWikitextSections()
    {
        if (!empty($this->wikitextSections)) {
            return $this->wikitextSections;
        }

        $title = $this->getMainSection()->getTitle();
        $this->wikitextSections = (new SectionsParser($title, $this->wikitext))->sections();
        $this->wikitextSections->each(function (Section $section) {
            $sanitized = (new Wikitext($section->getTitle()))->sanitize();
            $section->setTitle($sanitized);
        });

        return $this->wikitextSections;
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
