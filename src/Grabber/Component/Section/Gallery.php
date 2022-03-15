<?php

namespace Illuminated\Wikipedia\Grabber\Component\Section;

use Illuminate\Support\Collection;

class Gallery
{
    /**
     * Validate section gallery.
     */
    public function validate(Collection $gallery): array
    {
        $pure = ['gallery' => $gallery, 'not_gallery' => collect()];

        $byTypes = $this->byTypes($gallery);
        $main = $this->getMainType($byTypes);

        $pure['gallery'] = $byTypes[$main];
        foreach ($byTypes as $type => $collection) {
            if ($type != $main) {
                $pure['not_gallery'] = $pure['not_gallery']->merge($collection);
            }
        }

        if ($pure['gallery']->count() < 3) {
            $pure['gallery'] = collect();
            $pure['not_gallery'] = $gallery;
        }

        return $pure;
    }

    /**
     * Classify the given collection by types.
     */
    protected function byTypes(Collection $images): array
    {
        $result = ['video' => collect(), 'audio' => collect(), 'images' => collect()];

        foreach ($images as $image) {
            if ($image->isVideo()) {
                $result['video']->push($image);
                continue;
            }

            if ($image->isAudio()) {
                $result['audio']->push($image);
                continue;
            }

            $result['images']->push($image);
        }

        return $result;
    }

    /**
     * Get the main type of the gallery.
     */
    protected function getMainType(array $byTypes): mixed
    {
        $counts = collect($byTypes)->map(function (Collection $collection, $key) {
            return ['key' => $key, 'count' => $collection->count()];
        });

        $max = $counts->max('count');

        return $counts->first(function ($item) use ($max) {
            return $item['count'] == $max;
        })['key'];
    }
}
