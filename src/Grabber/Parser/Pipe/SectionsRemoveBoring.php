<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class SectionsRemoveBoring
{
    protected $sections;
    protected $boring;

    public function __construct(Collection $sections)
    {
        $this->sections = $sections;
        $this->boring = array_flatten(
            (array) config('wikipedia-grabber.boring_sections')
        );
    }

    public function pipe()
    {
        $filtered = collect();

        foreach ($this->sections as $section) {
            if ($section->isMain() || !$this->isBoring($section)) {
                $filtered->push($section);
            }
        }

        return $filtered;
    }

    protected function isBoring(Section $section)
    {
        return in_array($section->getTitle(), $this->boring);
    }
}
