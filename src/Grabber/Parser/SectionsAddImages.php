<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Image;
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
        $this->images = $this->getUsedImages($this->wikitext, $imagesResponseData['images']);
    }

    public function filter()
    {
        if ($this->noImages()) {
            return $this->sections;
        }

        foreach ($this->sections as $section) {
            if ($section->isMain()) {
                $section->setImages(
                    $this->createMainObject()
                );
            }

            $wikitextSection = $this->getWikitextSectionFor($section);
            if (empty($wikitextSection)) {
                continue;
            }

            $sectionImages = $this->getUsedImages($wikitextSection->getBody(), $this->images);
            if (empty($sectionImages)) {
                continue;
            }

            $section->setImages( ////////////////////////////////////////////////////////////////// ADD instead of SET!!
                $this->createObjects($wikitextSection, $sectionImages)
            );

            $this->freeUsedImages($sectionImages);
        }

        return $this->sections;
    }

    protected function noImages()
    {
        return empty($this->mainImage) && empty($this->images);
    }

    protected function getUsedImages($wikitext, array $images)
    {
        return collect($images)->filter(function (array $image) use ($wikitext) {
            return $this->isImageUsed($wikitext, $image);
        })->toArray();
    }

    protected function isImageUsed($wikitext, array $image)
    {
        $file = last(explode(':', $image['title']));

        return str_contains($wikitext, $file);
    }

    protected function createMainObject()
    {
        return collect([
            new Image(
                $this->mainImage['thumbnail']['source'],
                $this->mainImage['thumbnail']['width'],
                $this->mainImage['thumbnail']['height'],
                $this->mainImage['original']['source']
            ),
        ]);
    }

    protected function createObjects(Section $wikitextSection, array $images)
    {
        $wikitext = new Wikitext($wikitextSection->getBody());

        return collect($images)->map(function (array $image) use ($wikitext) {
            return $wikitext->createImageObject($image);
        });
    }

    protected function freeUsedImages(array $usedImages)
    {
        $usedImages = array_pluck($usedImages, 'title');
        $this->images = collect($this->images)->filter(function (array $image) use ($usedImages) {
            return !in_array($image['title'], $usedImages);
        })->toArray();
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
