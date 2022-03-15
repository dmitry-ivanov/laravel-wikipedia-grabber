<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class SectionsRemoveBoring
{
    /**
     * The sections.
     */
    protected Collection $sections;

    /**
     * The list of the boring titles.
     */
    protected array $boringTitles;

    /**
     * Create a new instance of the pipe.
     */
    public function __construct(Collection $sections)
    {
        $this->sections = $sections;
        $this->boringTitles = Arr::flatten(config('wikipedia-grabber.boring_sections', []));
    }

    /**
     * Execute the pipe.
     */
    public function pipe(): Collection
    {
        $filtered = collect();

        foreach ($this->sections as $section) {
            if ($section->isMain() || !$this->isBoring($section)) {
                $filtered->push($section);
            }
        }

        return $filtered;
    }

    /**
     * Check whether the given section is boring or not.
     */
    protected function isBoring(Section $section): bool
    {
        return in_array($section->getTitle(), $this->boringTitles);
    }
}
