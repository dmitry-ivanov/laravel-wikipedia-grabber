<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsRemoveEmpty
{
    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * Create a new instance of the pipe.
     *
     * @param \Illuminate\Support\Collection $sections
     * @return void
     */
    public function __construct(Collection $sections)
    {
        $this->sections = $sections;
    }

    /**
     * Execute the pipe.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pipe()
    {
        do {
            $filtered = $this->iteration();
            $isSomethingRemoved = ($filtered->count() != $this->sections->count());
            $this->sections = $filtered;
        } while ($isSomethingRemoved);

        return $this->sections;
    }

    /**
     * Do the iteration of filtering.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function iteration()
    {
        $filtered = collect();

        foreach ($this->sections as $index => $section) {
            if ($section->isMain() || !$section->isEmpty()) {
                $filtered->push($section);
                continue;
            }

            $isNextNotExists = empty($this->sections[$index + 1]);
            if ($isNextNotExists) {
                continue;
            }

            $level = $section->getLevel();
            $nextSection = $this->sections[$index + 1];
            $nextSectionLevel = $nextSection->getLevel();
            $hasChild = ($level < $nextSectionLevel);
            if ($hasChild) {
                $filtered->push($section);
            }
        }

        return $filtered;
    }
}
