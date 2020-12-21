<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Tests\TestCase;

class SectionTest extends TestCase
{
    /** @test */
    public function it_trims_passed_title()
    {
        $section = new Section('  Title FOO  ', 'Body', 1);
        $this->assertEquals('Title FOO', $section->getTitle());
    }

    /** @test */
    public function it_transform_passed_wikitext_to_plain_for_title()
    {
        $section = new Section("'''Title''' [[Url|With Link]] {{nobr|And Template}}<ref foo>bar</ref>", 'Body', 1);
        $this->assertEquals('Title With Link And Template', $section->getTitle());
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
    public function it_sets_images_to_empty_collection_by_default()
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertEquals($section->getImages(), collect());
    }

    /** @test */
    public function but_if_images_are_passed_then_they_would_be_set()
    {
        $section = new Section('Title', 'Body', '7', collect(['some', 'fake', 'images']));
        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    /** @test */
    public function it_sets_gallery_to_empty_collection_by_default()
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertEquals($section->getGallery(), collect());
    }

    /** @test */
    public function but_you_can_set_gallery_by_a_separate_call()
    {
        $section = new Section('Title', 'Body', '7');
        $section->setGallery(collect(['some', 'fake', 'gallery']));

        $this->assertEquals($section->getGallery(), collect(['some', 'fake', 'gallery']));
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
    public function it_has_is_empty_method_which_returns_true_if_body_and_images_and_gallery_are_empty()
    {
        $section = new Section('Title', '', 3, null);
        $section->setGallery(null);

        $this->assertTrue($section->isEmpty());
    }

    /** @test */
    public function and_if_body_is_not_empty_then_section_is_not_empty()
    {
        $section = new Section('Title', 'Not empty body', 3, null);
        $section->setGallery(null);

        $this->assertFalse($section->isEmpty());
    }

    /** @test */
    public function and_if_images_is_not_empty_then_section_is_not_empty()
    {
        $section = new Section('Title', '', 3, collect(['images', 'collection', 'here']));
        $section->setGallery(null);

        $this->assertFalse($section->isEmpty());
    }

    /** @test */
    public function and_if_gallery_is_not_empty_then_section_is_not_empty()
    {
        $section = new Section('Title', '', 3);
        $section->setGallery(collect(['fake', 'gallery']));

        $this->assertFalse($section->isEmpty());
    }

    /** @test */
    public function and_if_images_collection_is_empty_and_also_body_then_section_is_empty_too()
    {
        $section = new Section('Title', '', 3, collect([]));
        $this->assertTrue($section->isEmpty());
    }

    /** @test */
    public function it_has_has_images_method()
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertFalse($section->hasImages());
    }

    /** @test */
    public function which_returns_false_even_if_images_are_set_as_empty_collection()
    {
        $section = new Section('Title', 'Body', 7, collect());
        $this->assertFalse($section->hasImages());
    }

    /** @test */
    public function which_returns_true_if_section_has_images()
    {
        $section = new Section('Title', 'Body', 7, collect(['some', 'fake', 'images']));
        $this->assertTrue($section->hasImages());
    }

    /** @test */
    public function it_has_add_images_method()
    {
        $section = new Section('Title', 'Body', 7);
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    /** @test */
    public function which_works_even_if_images_are_set_initially_as_empty_collection()
    {
        $section = new Section('Title', 'Body', 7, collect());
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    /** @test */
    public function which_works_even_if_images_are_set_initially()
    {
        $section = new Section('Title', 'Body', 7, collect(['initial', 'images']));
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['initial', 'images', 'some', 'fake', 'images']));
    }

    /** @test */
    public function it_has_has_gallery_method()
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertFalse($section->hasGallery());
    }

    /** @test */
    public function which_returns_false_even_if_gallery_was_set_as_empty_collection()
    {
        $section = new Section('Title', 'Body', 7);
        $section->setGallery(collect());

        $this->assertFalse($section->hasGallery());
    }

    /** @test */
    public function which_returns_true_if_section_has_gallery()
    {
        $section = new Section('Title', 'Body', 7);
        $section->setGallery(collect(['some', 'fake', 'gallery']));

        $this->assertTrue($section->hasGallery());
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
