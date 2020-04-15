<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;
use Illuminated\Wikipedia\Tests\TestCase;

class SectionsParserTest extends TestCase
{
    /** @test */
    public function it_parses_collection_of_sections_from_passed_extract_body()
    {
        $body = file_get_contents(__DIR__ . '/SectionsParserTest/body.txt');
        $sections = (new SectionsParser('Page title', $body))->sections();

        $this->assertInstanceOf(Collection::class, $sections);

        $expected = require __DIR__ . '/SectionsParserTest/sections.php';
        $this->assertEquals($expected, $sections->toArray());
    }
}
