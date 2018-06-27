<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Formatter\MultilineFile;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class MultilineFileTest extends TestCase
{
    /** @test */
    public function it_has_flatten_method_which_flattens_multiline_files()
    {
        $body = trim(file_get_contents(__DIR__ . '/MultilineFileTest/body.txt'));
        $section = new Section('Title', $body, 2);

        $flatten = trim(file_get_contents(__DIR__ . '/MultilineFileTest/flatten.txt'));
        $this->assertEquals($flatten, (new MultilineFile)->flatten($section));
    }
}
