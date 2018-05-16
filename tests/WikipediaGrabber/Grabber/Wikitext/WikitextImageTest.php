<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\WikitextImage;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextImageTest extends TestCase
{
    /** @test */
    public function it_can_parse_simple_image_wikitext()
    {
        $image = new WikitextImage('[[File:Name.jpg|thumb|left]]');

        $this->assertEquals($image->getName(), 'File:Name.jpg');
        $this->assertEquals($image->getType(), 'thumb');
        $this->assertEquals($image->getBorder(), null);
        $this->assertEquals($image->getLocation(), 'left');
        $this->assertEquals($image->getAlignment(), null);
        $this->assertEquals($image->getSize(), null);
        $this->assertEquals($image->getLink(), null);
        $this->assertEquals($image->getAlt(), null);
        $this->assertEquals($image->getLangtag(), null);
        $this->assertEquals($image->getCaption(), null);
    }
}
