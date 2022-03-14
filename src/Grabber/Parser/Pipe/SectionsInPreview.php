<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsInPreview
{
    /**
     * The sections.
     */
    protected Collection $sections;

    /**
     * Is in preview mode.
     */
    protected bool $isPreview;

    /**
     * Create a new instance of the pipe.
     */
    public function __construct(Collection $sections, bool $isPreview)
    {
        $this->sections = $sections;
        $this->isPreview = $isPreview;
    }

    /**
     * Execute the pipe.
     */
    public function pipe(): Collection
    {
        if ($this->isPreview) {
            $this->sections->first()->setTitle('');
        }

        return $this->sections;
    }
}
