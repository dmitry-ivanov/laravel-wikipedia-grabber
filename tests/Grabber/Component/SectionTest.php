<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Component;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SectionTest extends TestCase
{
    #[Test]
    public function it_trims_passed_title(): void
    {
        $section = new Section('  Title FOO  ', 'Body', 1);
        $this->assertEquals('Title FOO', $section->getTitle());
    }

    #[Test]
    public function it_transform_passed_wikitext_to_plain_for_title(): void
    {
        $section = new Section("'''Title''' [[Url|With Link]] {{nobr|And Template}}<ref foo>bar</ref>", 'Body', 1);
        $this->assertEquals('Title With Link And Template', $section->getTitle());
    }

    #[Test]
    public function it_trims_passed_body(): void
    {
        $section = new Section('Title', '  Body FOO  ', 1);
        $this->assertEquals('Body FOO', $section->getBody());
    }

    #[Test]
    public function it_converts_passed_level_to_int(): void
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertSame(7, $section->getLevel());
    }

    #[Test]
    public function and_if_level_is_less_than_1_then_it_would_be_1(): void
    {
        $section = new Section('Title', 'Body', 0);
        $this->assertEquals(1, $section->getLevel());
    }

    #[Test]
    public function it_sets_images_to_empty_collection_by_default(): void
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertEquals($section->getImages(), collect());
    }

    #[Test]
    public function but_if_images_are_passed_then_they_would_be_set(): void
    {
        $section = new Section('Title', 'Body', '7', collect(['some', 'fake', 'images']));
        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    #[Test]
    public function it_sets_gallery_to_empty_collection_by_default(): void
    {
        $section = new Section('Title', 'Body', '7');
        $this->assertEquals($section->getGallery(), collect());
    }

    #[Test]
    public function but_you_can_set_gallery_by_a_separate_call(): void
    {
        $section = new Section('Title', 'Body', '7');
        $section->setGallery(collect(['some', 'fake', 'gallery']));

        $this->assertEquals($section->getGallery(), collect(['some', 'fake', 'gallery']));
    }

    #[Test]
    public function it_has_is_main_method(): void
    {
        $section = new Section('Title', 'Body', 3);
        $this->assertFalse($section->isMain());
    }

    #[Test]
    public function which_returns_true_only_if_section_level_is_1(): void
    {
        $section = new Section('Title', 'Body', 1);
        $this->assertTrue($section->isMain());
    }

    #[Test]
    public function it_has_is_empty_method_which_returns_true_if_body_and_images_and_gallery_are_empty(): void
    {
        $section = new Section('Title', '', 3, null);
        $section->setGallery(null);

        $this->assertTrue($section->isEmpty());
    }

    #[Test]
    public function and_if_body_is_not_empty_then_section_is_not_empty(): void
    {
        $section = new Section('Title', 'Not empty body', 3, null);
        $section->setGallery(null);

        $this->assertFalse($section->isEmpty());
    }

    #[Test]
    public function and_if_images_is_not_empty_then_section_is_not_empty(): void
    {
        $section = new Section('Title', '', 3, collect(['images', 'collection', 'here']));
        $section->setGallery(null);

        $this->assertFalse($section->isEmpty());
    }

    #[Test]
    public function and_if_gallery_is_not_empty_then_section_is_not_empty(): void
    {
        $section = new Section('Title', '', 3);
        $section->setGallery(collect(['fake', 'gallery']));

        $this->assertFalse($section->isEmpty());
    }

    #[Test]
    public function and_if_images_collection_is_empty_and_also_body_then_section_is_empty_too(): void
    {
        $section = new Section('Title', '', 3, collect([]));
        $this->assertTrue($section->isEmpty());
    }

    #[Test]
    public function it_has_has_images_method(): void
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertFalse($section->hasImages());
    }

    #[Test]
    public function which_returns_false_even_if_images_are_set_as_empty_collection(): void
    {
        $section = new Section('Title', 'Body', 7, collect());
        $this->assertFalse($section->hasImages());
    }

    #[Test]
    public function which_returns_true_if_section_has_images(): void
    {
        $section = new Section('Title', 'Body', 7, collect(['some', 'fake', 'images']));
        $this->assertTrue($section->hasImages());
    }

    #[Test]
    public function it_has_add_images_method(): void
    {
        $section = new Section('Title', 'Body', 7);
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    #[Test]
    public function which_works_even_if_images_are_set_initially_as_empty_collection(): void
    {
        $section = new Section('Title', 'Body', 7, collect());
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['some', 'fake', 'images']));
    }

    #[Test]
    public function which_works_even_if_images_are_set_initially(): void
    {
        $section = new Section('Title', 'Body', 7, collect(['initial', 'images']));
        $section->addImages(collect(['some', 'fake', 'images']));

        $this->assertEquals($section->getImages(), collect(['initial', 'images', 'some', 'fake', 'images']));
    }

    #[Test]
    public function it_has_has_gallery_method(): void
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertFalse($section->hasGallery());
    }

    #[Test]
    public function which_returns_false_even_if_gallery_was_set_as_empty_collection(): void
    {
        $section = new Section('Title', 'Body', 7);
        $section->setGallery(collect());

        $this->assertFalse($section->hasGallery());
    }

    #[Test]
    public function which_returns_true_if_section_has_gallery(): void
    {
        $section = new Section('Title', 'Body', 7);
        $section->setGallery(collect(['some', 'fake', 'gallery']));

        $this->assertTrue($section->hasGallery());
    }

    #[Test]
    public function it_has_get_html_level_method(): void
    {
        $section = new Section('Title', 'Body', 3);
        $this->assertEquals(3, $section->getHtmlLevel());
    }

    #[Test]
    public function which_will_return_6_if_level_is_greater_than_6(): void
    {
        $section = new Section('Title', 'Body', 7);
        $this->assertEquals(6, $section->getHtmlLevel());
    }
}
