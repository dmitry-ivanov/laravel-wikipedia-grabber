<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsInPreview
{
    protected $sections;
    protected $isPreview;

    public function __construct(Collection $sections, $isPreview)
    {
        $this->sections = $sections;
        $this->isPreview = $isPreview;
    }

    public function pipe()
    {
        if ($this->isPreview) {
            $this->sections->first()->setTitle('');
        }

        return $this->sections;
    }
}
