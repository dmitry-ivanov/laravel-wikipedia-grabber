<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;

class GalleryValidator
{
    protected $gallery;
    protected $minCount = 4;

    public function __construct(Collection $gallery)
    {
        $this->gallery = $gallery;
    }

    public function validate()
    {
        $validated = ['gallery' => $this->gallery, 'not_gallery' => collect()];

        if ($validated['gallery']->count() < $this->minCount) {
            $validated['gallery'] = collect();
            $validated['not_gallery'] = $this->gallery;
        }

        return $validated;
    }
}
