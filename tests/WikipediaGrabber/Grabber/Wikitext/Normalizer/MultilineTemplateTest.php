<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineTemplate;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class MultilineTemplateTest extends TestCase
{
    /** @test */
    public function it_has_flatten_method_which_flattens_multiline_templates()
    {
        $body = trim(file_get_contents(__DIR__ . '/MultilineTemplateTest/body.txt'));
        $section = new Section('Title', $body, 2);

        $flatten = trim(file_get_contents(__DIR__ . '/MultilineTemplateTest/flatten.txt'));
        $this->assertEquals($flatten, (new MultilineTemplate)->flatten($section));
    }
}
