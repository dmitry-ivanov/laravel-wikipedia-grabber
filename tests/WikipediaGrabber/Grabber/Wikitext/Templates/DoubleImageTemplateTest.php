<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\DoubleImageTemplate;

class DoubleImageTemplateTest extends TestCase
{
    /** @test */
    public function it_has_extract_method_which_works_for_the_case_of_single_description()
    {
        $template = new DoubleImageTemplate('{{double image|right|Yellow card.svg|60|Red card.svg|60|Single description||Yellow|Red}}');

        $this->assertEquals($template->extract('Yellow card.svg'), 'Yellow card.svg|right|Single description');
        $this->assertEquals($template->extract('Red card.svg'), 'Red card.svg|right|Single description');
    }

    /** @test */
    public function and_it_works_for_ru_wikitext_too()
    {
        $template = new DoubleImageTemplate('{{сдвоенное изображение|право|Yellow card.svg|60|Red card.svg|60||Single description|Yellow|Red}}');

        $this->assertEquals($template->extract('Yellow card.svg'), 'Yellow card.svg|право|Single description');
        $this->assertEquals($template->extract('Red card.svg'), 'Red card.svg|право|Single description');
    }

    /** @test */
    public function extract_method_also_works_for_the_case_with_different_captions()
    {
        $template = new DoubleImageTemplate('{{double image|left|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals($template->extract('Yellow card.svg'), 'Yellow card.svg|left|Caption of Yellow');
        $this->assertEquals($template->extract('Red card.svg'), 'Red card.svg|left|Caption of Red');
    }

    /** @test */
    public function that_case_is_handled_for_ru_wikitext_too()
    {
        $template = new DoubleImageTemplate('{{сдвоенное изображение|лево|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals($template->extract('Yellow card.svg'), 'Yellow card.svg|лево|Caption of Yellow');
        $this->assertEquals($template->extract('Red card.svg'), 'Red card.svg|лево|Caption of Red');
    }

    /** @test */
    public function it_will_return_initial_template_if_image_is_neither_left_nor_right()
    {
        $template = new DoubleImageTemplate('{{double image|right|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals(
            $template->extract('Blue card.svg'),
            '{{double image|right|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}'
        );
    }
}
