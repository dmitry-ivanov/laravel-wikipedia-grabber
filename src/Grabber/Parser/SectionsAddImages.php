<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;

class SectionsAddImages
{
    protected $sections;
    protected $images;

    public function __construct(Collection $sections, array $images = null)
    {
        $this->sections = $sections;
        $this->images = $images;
    }

    public function filter()
    {
        if (empty($this->images)) {
            return $this->sections;
        }

        $wikitextSections = $this->getWikitextSections();

        dd($wikitextSections);
        dd($this->images);

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }

    protected function getWikitextSections()
    {
        $main = $this->getMainSection();
        $wikitext = $this->images['wikitext'];

        return (new SectionsParser($main->getTitle(), $wikitext))->sections();
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
