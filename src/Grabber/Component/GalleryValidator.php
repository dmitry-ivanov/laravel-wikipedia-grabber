<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;

class GalleryValidator
{
    public function validate(Collection $gallery)
    {
        $validated = ['gallery' => $gallery, 'not_gallery' => collect()];

        if ($validated['gallery']->count() < 4) {
            $validated['gallery'] = collect();
            $validated['not_gallery'] = $gallery;
        }

        return $validated;
    }
}
