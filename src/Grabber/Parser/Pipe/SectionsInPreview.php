<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsInPreview
{
    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * Is in preview mode.
     *
     * @var bool
     */
    protected $isPreview;

    /**
     * Create a new instance of the pipe.
     *
     * @param \Illuminate\Support\Collection $sections
     * @param bool $isPreview
     * @return void
     */
    public function __construct(Collection $sections, bool $isPreview)
    {
        $this->sections = $sections;
        $this->isPreview = $isPreview;
    }

    /**
     * Execute the pipe.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pipe()
    {
        if ($this->isPreview) {
            $this->sections->first()->setTitle('');
        }

        return $this->sections;
    }
}
