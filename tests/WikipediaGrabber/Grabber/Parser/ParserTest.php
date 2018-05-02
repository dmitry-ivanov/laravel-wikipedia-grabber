<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Parser\Parser;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_can_parse_body_for_plain_format_also_excluding_an_empty_and_boring_sections()
    {
        $body = file_get_contents(__DIR__ . '/ParserTest/extract.txt');
        // $imagesResponseData = [
        //     'wikitext' => file_get_contents(__DIR__ . '/ParserTest/wikitext.txt'),
        //     'main_image' => require_once __DIR__ . '/ParserTest/main_image.php',
        //     'images' => require_once __DIR__ . '/ParserTest/images.php',
        // ];
        $imagesResponseData = null;

        $parsed = (new Parser('Page title', $body, $imagesResponseData))->parse('plain');

        $expects = file_get_contents(__DIR__ . '/ParserTest/plain.html');
        $this->assertEquals($expects, $parsed);
    }
}
