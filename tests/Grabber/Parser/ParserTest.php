<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Parser;

use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Grabber\Parser\Parser;

class ParserTest extends TestCase
{
    /** @test */
    public function it_can_parse_body_for_plain_format_also_excluding_an_empty_and_boring_sections()
    {
        $body = file_get_contents(__DIR__ . '/ParserTest/extract.txt');
        $imagesResponseData = [
            'wikitext' => file_get_contents(__DIR__ . '/ParserTest/wikitext.txt'),
            'main_image' => require __DIR__ . '/ParserTest/main_image.php',
            'images' => require __DIR__ . '/ParserTest/images.php',
        ];

        $parsed = (new Parser('Александр Сергеевич Пушкин', $body, $imagesResponseData))->parse('plain');

        $expects = file_get_contents(__DIR__ . '/ParserTest/plain.html');
        $this->assertEquals($expects, $parsed);
    }

    /** @test */
    public function it_can_parse_body_for_bulma_format_also_excluding_an_empty_and_boring_sections()
    {
        $body = file_get_contents(__DIR__ . '/ParserTest/extract.txt');
        $imagesResponseData = [
            'wikitext' => file_get_contents(__DIR__ . '/ParserTest/wikitext.txt'),
            'main_image' => require __DIR__ . '/ParserTest/main_image.php',
            'images' => require __DIR__ . '/ParserTest/images.php',
        ];

        $parsed = (new Parser('Александр Сергеевич Пушкин', $body, $imagesResponseData))->parse('bulma');

        $expects = file_get_contents(__DIR__ . '/ParserTest/bulma.html');
        $this->assertEquals($expects, $parsed);
    }

    /** @test */
    public function it_can_parse_body_for_bootstrap_format_also_excluding_an_empty_and_boring_sections()
    {
        $body = file_get_contents(__DIR__ . '/ParserTest/extract.txt');
        $imagesResponseData = [
            'wikitext' => file_get_contents(__DIR__ . '/ParserTest/wikitext.txt'),
            'main_image' => require __DIR__ . '/ParserTest/main_image.php',
            'images' => require __DIR__ . '/ParserTest/images.php',
        ];

        $parsed = (new Parser('Александр Сергеевич Пушкин', $body, $imagesResponseData))->parse('bootstrap');

        $expects = file_get_contents(__DIR__ . '/ParserTest/bootstrap.html');
        $this->assertEquals($expects, $parsed);
    }
}
