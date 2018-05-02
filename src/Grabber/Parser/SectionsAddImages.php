<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class SectionsAddImages
{
    protected $sections;
    protected $wikitext;
    protected $mainImage;
    protected $images;
    protected $wikitextSections;

    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        $this->sections = $sections;

        if (empty($imagesResponseData)) {
            return;
        }

        $this->wikitext = $imagesResponseData['wikitext'];
        $this->mainImage = $imagesResponseData['main_image'];
        $this->images = $this->filterImages($imagesResponseData['images']);
    }

    public function filter()
    {
        if ($this->noImages()) {
            return $this->sections;
        }

        foreach ($this->sections as $section) {
            $wikitextSection = $this->getWikitextSectionFor($section);
            if (empty($wikitextSection)) {
                continue;
            }

            dd($section, $wikitextSection, $this->images);
        }

        dd('filter method');

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }

    protected function noImages()
    {
        return empty($this->mainImage) && empty($this->images);
    }

    protected function filterImages(array $images)
    {
        $filtered = [];

        foreach ($images as $image) {
            $file = last(explode(':', $image['title']));
            if (str_contains($this->wikitext, $file)) {
                $filtered[] = $image;
            }
        }

        return $filtered;
    }

    protected function getWikitextSectionFor(Section $section)
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
