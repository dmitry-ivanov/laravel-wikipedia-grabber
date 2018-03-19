<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Parser\Parser;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_can_parse_body_for_plain_format()
    {
        $body = file_get_contents(__DIR__ . '/ParserTest/body.txt');
        $parsed = (new Parser('Page title', $body))->parse('plain');

        $expects = file_get_contents(__DIR__ . '/ParserTest/html.txt');
        $this->assertEquals($expects, $parsed);
    }
}
