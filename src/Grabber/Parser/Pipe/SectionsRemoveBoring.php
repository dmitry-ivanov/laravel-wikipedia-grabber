<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class SectionsRemoveBoring
{
    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * The list of the boring titles.
     *
     * @var array
     */
    protected $boringTitles;

    /**
     * Create a new instance of the pipe.
     *
     * @param \Illuminate\Support\Collection $sections
     * @return void
     */
    public function __construct(Collection $sections)
    {
        $this->sections = $sections;
        $this->boringTitles = Arr::flatten(config('wikipedia-grabber.boring_sections', []));
    }

    /**
     * Execute the pipe.
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Check whether the given section is boring or not.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $section
     * @return bool
     */
    protected function isBoring(Section $section)
    {
        return in_array($section->getTitle(), $this->boringTitles);
    }
}
