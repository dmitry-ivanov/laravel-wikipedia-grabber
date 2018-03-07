<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Parser;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_can_parse_body_for_plain_format()
    {
        $body = file_get_contents('./ParserTest/body.txt');

        $parsed = (new Parser($body))->parse('plain');

        dd($parsed);
    }
}
