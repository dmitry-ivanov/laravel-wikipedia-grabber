<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class SectionsParserTest extends TestCase
{
    /** @test */
    public function it_parses_sections_from_passed_extract_body()
    {
        $body = file_get_contents(__DIR__ . '/SectionsParserTest/body-1.txt');
        $sections = (new SectionsParser('Page title', $body))->sections();

        $this->assertInstanceOf(Collection::class, $sections);

        $expected = require_once __DIR__ . '/SectionsParserTest/sections-1.php';
        $this->assertEquals($expected, $sections->toArray());
    }
}
