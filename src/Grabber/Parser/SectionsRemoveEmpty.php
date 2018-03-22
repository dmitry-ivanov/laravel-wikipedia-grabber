<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;

class SectionsRemoveEmpty
{
    protected $sections;

    public function __construct(Collection $sections)
    {
        $this->sections = $sections;
    }

    public function filter()
    {
        do {
            $filtered = $this->iteration();
            $isSomethingRemoved = ($filtered->count() != $this->sections->count());
            $this->sections = $filtered;
        } while ($isSomethingRemoved);

        return $this->sections;
    }

    private function iteration()
    {
        $filtered = collect();

        foreach ($this->sections as $index => $section) {
            $body = $section->getBody();
            $level = $section->getLevel();

            if ($section->isMain() || !empty($body)) {
                $filtered->push($section);
                continue;
            }

            $isNextNotExists = empty($this->sections[$index + 1]);
            if ($isNextNotExists) {
                continue;
            }

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
