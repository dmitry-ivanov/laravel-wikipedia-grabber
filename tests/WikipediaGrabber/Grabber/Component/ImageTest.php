<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ImageTest extends TestCase
{
    /** @test */
    public function it_has_position_which_defaults_to_right()
    {
        $image = new Image('url', 100, 200, 'original');
        $this->assertEquals('right', $image->getPosition());
    }

    /** @test */
    public function position_can_be_set_to_left_value()
    {
        $image = new Image('url', 100, 200, 'original', 'left');
        $this->assertEquals('left', $image->getPosition());
    }

    /** @test */
    public function position_can_be_set_to_gallery_value()
    {
        $image = new Image('url', 100, 200, 'original', 'gallery');
        $this->assertEquals('gallery', $image->getPosition());
    }

    /** @test */
    public function but_if_passed_position_is_unknown_it_would_be_set_to_right()
    {
        $image = new Image('url', 100, 200, 'original', 'foobar');
        $this->assertEquals('right', $image->getPosition());
    }
}
