<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class SectionTest extends TestCase
{
    /** @test */
    public function it_trims_passed_title()
    {
        $section = new Section('  Title FOO  ', 'Body', 1);
        $this->assertEquals('Title FOO', $section->getTitle());
    }

    /** @test */
    public function it_trims_passed_body()
    {
        $section = new Section('Title', '  Body FOO  ', 1);
        $this->assertEquals('Body FOO', $section->getBody());
    }

    /** @test */
    public function it_converts_passed_level_to_int()
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertSame(7, $section->getLevel());
    }

    /** @test */
    public function and_if_level_is_less_than_1_then_it_would_be_1()
    {
        $section = new Section('Title', 'Body', 0);
        $this->assertEquals(1, $section->getLevel());
    }

    /** @test */
    public function it_has_is_main_method()
    {
        $section = new Section('Title', 'Body', 3);
        $this->assertFalse($section->isMain());
    }

    /** @test */
    public function which_returns_true_only_if_section_level_is_1()
    {
        $section = new Section('Title', 'Body', 1);
        $this->assertTrue($section->isMain());
    }

    /** @test */
    public function it_has_get_html_level_method()
    {
        $section = new Section('Title', 'Body', 3);
        $this->assertEquals(3, $section->getHtmlLevel());
    }

    /** @test */
    public function which_will_return_6_if_level_is_greater_than_6()
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertEquals(6, $section->getHtmlLevel());
    }
}
