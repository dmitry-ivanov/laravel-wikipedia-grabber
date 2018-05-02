<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;

class SectionsAddImages
{
    protected $sections;
    protected $wikitextSections;
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

        dd($this->imagesResponseData);

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }

    protected function getWikitextSections()
    {
        if (!empty($this->wikitextSections)) {
            return $this->wikitextSections;
        }

        $main = $this->getMainSection();
        $wikitext = $this->imagesResponseData['wikitext'];
        $this->wikitextSections = (new SectionsParser($main->getTitle(), $wikitext))->sections();

        return $this->wikitextSections;
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
