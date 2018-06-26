<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;

class GalleryValidator
{
    public function validate(Collection $gallery)
    {
        $validated = ['gallery' => $gallery, 'not_gallery' => collect()];

        $byTypes = $this->byTypes($gallery);
        $main = $this->getMainType($byTypes);

        $validated['gallery'] = $byTypes[$main];
        foreach ($byTypes as $type => $collection) {
            if ($type != $main) {
                $validated['not_gallery'] = $validated['not_gallery']->merge($collection);
            }
        }

        if ($validated['gallery']->count() < 4) {
            $validated['gallery'] = collect();
            $validated['not_gallery'] = $gallery;
        }

        return $validated;
    }

    protected function byTypes(Collection $images)
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

    protected function getMainType(array $byTypes)
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
