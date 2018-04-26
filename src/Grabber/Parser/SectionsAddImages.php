<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;

class SectionsAddImages
{
    protected $sections;
    protected $imagesResponseData;

    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        $this->sections = $sections;
        $this->imagesResponseData = $imagesResponseData;
    }

    public function filter()
    {
        if (empty($this->imagesResponseData)) {
            return $this->sections;
        }

        $wikitextSections = $this->getWikitextSections();

        dd($wikitextSections);
        dd($this->imagesResponseData);

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }

    protected function getWikitextSections()
    {
        $main = $this->getMainSection();
        $wikitext = $this->imagesResponseData['wikitext'];

        return (new SectionsParser($main->getTitle(), $wikitext))->sections();
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
