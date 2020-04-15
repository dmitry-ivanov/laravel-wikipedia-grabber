<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\DoubleImageTemplate;
use Illuminated\Wikipedia\Tests\TestCase;

class DoubleImageTemplateTest extends TestCase
{
    /** @test */
    public function it_has_extract_method_which_works_for_the_case_of_single_description()
    {
        $template = new DoubleImageTemplate('{{double image|right|Yellow card.svg|60|Red card.svg|60|Single description||Yellow|Red}}');

        $this->assertEquals('Yellow card.svg|right|Single description', $template->extract('Yellow card.svg'));
        $this->assertEquals('Red card.svg|right|Single description', $template->extract('Red card.svg'));
    }

    /** @test */
    public function and_it_works_for_ru_wikitext_too()
    {
        $template = new DoubleImageTemplate('{{сдвоенное изображение|право|Yellow card.svg|60|Red card.svg|60||Single description|Yellow|Red}}');

        $this->assertEquals('Yellow card.svg|право|Single description', $template->extract('Yellow card.svg'));
        $this->assertEquals('Red card.svg|право|Single description', $template->extract('Red card.svg'));
    }

    /** @test */
    public function extract_method_also_works_for_the_case_with_different_captions()
    {
        $template = new DoubleImageTemplate('{{double image|left|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals('Yellow card.svg|left|Caption of Yellow', $template->extract('Yellow card.svg'));
        $this->assertEquals('Red card.svg|left|Caption of Red', $template->extract('Red card.svg'));
    }

    /** @test */
    public function that_case_is_handled_for_ru_wikitext_too()
    {
        $template = new DoubleImageTemplate('{{сдвоенное изображение|лево|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals('Yellow card.svg|лево|Caption of Yellow', $template->extract('Yellow card.svg'));
        $this->assertEquals('Red card.svg|лево|Caption of Red', $template->extract('Red card.svg'));
    }

    /** @test */
    public function it_will_return_initial_template_if_image_is_neither_left_nor_right()
    {
        $template = new DoubleImageTemplate('{{double image|right|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}');

        $this->assertEquals(
            '{{double image|right|Yellow card.svg|60|Red card.svg|60|Caption of Yellow|Caption of Red|Yellow|Red}}',
            $template->extract('Blue card.svg')
        );
    }
}
