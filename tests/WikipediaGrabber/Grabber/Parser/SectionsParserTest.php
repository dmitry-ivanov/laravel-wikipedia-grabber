<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class SectionsParserTest extends TestCase
{
    /** @test */
    public function it_()
    {
        $body = file_get_contents('./SectionsParserTest/body.txt');

        $sections = (new SectionsParser($body))->sections();

        dd($sections);
    }
}
