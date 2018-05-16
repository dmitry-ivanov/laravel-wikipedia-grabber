<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\WikitextImage;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextImageTest extends TestCase
{
    /** @test */
    public function it_can_parse_simple_image_wikitext()
    {
        $image = new WikitextImage('[[File:Name.jpg]]');

        $this->assertEquals($image->getName(), 'File:Name.jpg');
        $this->assertEquals($image->getType(), null);
        $this->assertEquals($image->getBorder(), null);
        $this->assertEquals($image->getLocation(), null);
        $this->assertEquals($image->getAlignment(), null);
        $this->assertEquals($image->getSize(), null);
        $this->assertEquals($image->getLink(), null);
        $this->assertEquals($image->getAlt(), null);
        $this->assertEquals($image->getLangtag(), null);
        $this->assertEquals($image->getCaption(), null);
    }

    /** @test */
    public function it_can_parse_image_wikitext_with_few_params()
    {
        $image = new WikitextImage('[[File:Name.jpg|thumb|left|200px]]');

        $this->assertEquals($image->getName(), 'File:Name.jpg');
        $this->assertEquals($image->getType(), 'thumb');
        $this->assertEquals($image->getBorder(), null);
        $this->assertEquals($image->getLocation(), 'left');
        $this->assertEquals($image->getAlignment(), null);
        $this->assertEquals($image->getSize(), '200px');
        $this->assertEquals($image->getLink(), null);
        $this->assertEquals($image->getAlt(), null);
        $this->assertEquals($image->getLangtag(), null);
        $this->assertEquals($image->getCaption(), null);
    }

    /** @test */
    public function params_can_be_mixed_in_any_order()
    {
        $image = new WikitextImage('[[File:Name.jpg|left|thumbnail=foo|border|upright|lang=foo|text-bottom|alt=foo|link=foo|Image Caption|page=11]]');

        $this->assertEquals($image->getName(), 'File:Name.jpg');
        $this->assertEquals($image->getType(), 'thumbnail=foo');
        $this->assertEquals($image->getBorder(), 'border');
        $this->assertEquals($image->getLocation(), 'left');
        $this->assertEquals($image->getAlignment(), 'text-bottom');
        $this->assertEquals($image->getSize(), 'upright');
        $this->assertEquals($image->getLink(), 'link=foo');
        $this->assertEquals($image->getAlt(), 'alt=foo');
        $this->assertEquals($image->getLangtag(), 'lang=foo');
        $this->assertEquals($image->getCaption(), 'Image Caption');
    }
}
