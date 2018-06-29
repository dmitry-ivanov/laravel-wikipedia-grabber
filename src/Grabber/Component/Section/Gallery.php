<?php

namespace Illuminated\Wikipedia\Grabber\Component\Section;

use Illuminate\Support\Collection;

class Gallery
{
    public function validate(Collection $gallery)
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
