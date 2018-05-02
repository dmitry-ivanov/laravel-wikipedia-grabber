<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextTest extends TestCase
{
    /** @test */
    public function it_can_sanitize_wikitext_by_removing_links()
    {
        $this->assertEquals(
            'Link Title',
            (new Wikitext('[[Link Href|Link Title]]'))->sanitize()
        );
    }

    /** @test */
    public function which_works_for_simple_links_too()
    {
        $this->assertEquals(
            'Some link',
            (new Wikitext('[[Some link]]'))->sanitize()
        );
    }

    /** @test */
    public function which_works_for_multiple_links_too()
    {
        $this->assertEquals(
            'This is Link1, and this is Link2, and Link3',
            (new Wikitext('This is [[Super Link|Link1]], and this is [[Wow Link|Link2]], and [[Link3]]'))->sanitize()
        );
    }

    /** @test */
    public function which_works_for_wikitext_without_links_too()
    {
        $this->assertEquals(
            'This is wikitext without links',
            (new Wikitext('This is wikitext without links'))->sanitize()
        );
    }

    /** @test */
    public function which_works_for_multiline_wikitext_too()
    {
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline_sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->sanitize());
    }
}
