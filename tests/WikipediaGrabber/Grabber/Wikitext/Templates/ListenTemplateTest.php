<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ListenTemplate;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ListenTemplateTest extends TestCase
{
    /** @test */
    public function it_has_transform_method_which_works_for_the_case_of_title()
    {
        $template = new ListenTemplate("{{Listen | type = music | filename = Accordion chords-01.ogg | title = Accordion chords }}");

        $this->assertEquals(
            $template->transform(),
            '{{Listen|type = music|filename = Accordion chords-01.ogg|title=Accordion chords}}'
        );
    }

    /** @test */
    public function it_has_transform_method_which_works_for_the_case_of_description()
    {
        $template = new ListenTemplate("{{Listen | type = music | filename = Accordion chords-01.ogg | description = Chords being played on an accordion }}");

        $this->assertEquals(
            $template->transform(),
            '{{Listen|type = music|filename = Accordion chords-01.ogg|title=Chords being played on an accordion}}'
        );
    }

    /** @test */
    public function it_has_transform_method_which_works_for_the_case_of_title_and_description()
    {
        $template = new ListenTemplate("{{Listen | type = music | filename = Accordion chords-01.ogg | title = Accordion chords | description = Chords being played on an accordion }}");

        $this->assertEquals(
            $template->transform(),
            '{{Listen|type = music|filename = Accordion chords-01.ogg|title=Accordion chords - Chords being played on an accordion}}'
        );
    }
}
