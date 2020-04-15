<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ListenTemplate;
use Illuminated\Wikipedia\Tests\TestCase;

class ListenTemplateTest extends TestCase
{
    /** @test */
    public function it_has_transform_method_which_works_if_there_is_no_title_and_description()
    {
        $template = new ListenTemplate('{{Listen | type = music | filename = Accordion chords-01.ogg | footer = Some footer }}');

        $this->assertEquals(
            '{{Listen|type = music|filename = Accordion chords-01.ogg|footer = Some footer}}',
            $template->transform()
        );
    }

    /** @test */
    public function and_it_works_for_the_case_when_only_title_set()
    {
        $template = new ListenTemplate('{{Listen | type = music | filename = Accordion chords-01.ogg | Title = Accordion chords | footer = Some footer }}');

        $this->assertEquals(
            '{{Listen|type = music|filename = Accordion chords-01.ogg|footer = Some footer|title=Accordion chords}}',
            $template->transform()
        );
    }

    /** @test */
    public function and_it_works_for_the_case_when_only_description_set()
    {
        $template = new ListenTemplate('{{Listen | type = music | filename = Accordion chords-01.ogg | Description = Chords being played on an accordion | footer = Some footer }}');

        $this->assertEquals(
            '{{Listen|type = music|filename = Accordion chords-01.ogg|footer = Some footer|title=Chords being played on an accordion}}',
            $template->transform()
        );
    }

    /** @test */
    public function and_it_works_for_the_case_when_title_and_description_are_set()
    {
        $template = new ListenTemplate('{{Listen | type = music | filename = Accordion chords-01.ogg | Title = Accordion chords | Description = Chords being played on an accordion | footer = Some footer }}');

        $this->assertEquals(
            '{{Listen|type = music|filename = Accordion chords-01.ogg|footer = Some footer|title=Accordion chords - Chords being played on an accordion}}',
            $template->transform()
        );
    }
}
