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

        return true; ///////////////////////////////////////////////////////////////////////////////////////////////////
    }
}
