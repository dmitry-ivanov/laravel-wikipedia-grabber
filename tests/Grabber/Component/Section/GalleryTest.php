<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Component\Section;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section\Gallery;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GalleryTest extends TestCase
{
    #[Test]
    public function it_has_validate_method_which_checks_min_items_count_for_gallery(): void
    {
        $collection = collect([
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
        ]);

        $this->assertEquals(
            ['gallery' => collect(), 'not_gallery' => $collection],
            (new Gallery)->validate($collection)
        );
    }

    #[Test]
    public function min_count_for_gallery_is_3(): void
    {
        $collection = collect([
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
        ]);

        $this->assertEquals(
            ['gallery' => $collection, 'not_gallery' => collect()],
            (new Gallery)->validate($collection)
        );
    }

    #[Test]
    public function it_prevents_mixed_collections(): void
    {
        $collection = collect([
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file.ogv'),
        ]);

        $this->assertEquals(
            ['gallery' => collect(), 'not_gallery' => $collection],
            (new Gallery)->validate($collection)
        );
    }

    #[Test]
    public function it_filters_mixed_collection_by_types(): void
    {
        $images = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.jpg'),
            new Image('thumb', 200, 200, 'http://example.com/file2.jpg'),
        ]);

        $audio = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file2.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file3.oga'),
        ]);

        $video = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file2.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file3.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file4.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file5.ogv'),
        ]);

        $collection = $images->merge($audio)->merge($video);

        $this->assertEquals(
            ['gallery' => $video, 'not_gallery' => $audio->merge($images)],
            (new Gallery)->validate($collection)
        );
    }

    #[Test]
    public function when_few_types_have_similar_counts_they_sorted_by_priority(): void
    {
        $images = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.jpg'),
            new Image('thumb', 200, 200, 'http://example.com/file2.jpg'),
            new Image('thumb', 200, 200, 'http://example.com/file3.jpg'),
            new Image('thumb', 200, 200, 'http://example.com/file4.jpg'),
            new Image('thumb', 200, 200, 'http://example.com/file5.jpg'),
        ]);

        $audio = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file2.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file3.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file4.oga'),
            new Image('thumb', 200, 200, 'http://example.com/file5.oga'),
        ]);

        $video = collect([
            new Image('thumb', 200, 200, 'http://example.com/file1.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file2.ogv'),
            new Image('thumb', 200, 200, 'http://example.com/file3.ogv'),
        ]);

        $collection = $images->merge($audio)->merge($video);

        $this->assertEquals(
            ['gallery' => $audio, 'not_gallery' => $video->merge($images)],
            (new Gallery)->validate($collection)
        );
    }
}
