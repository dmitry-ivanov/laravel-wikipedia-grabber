<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\WikitextImage;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextImageTest extends TestCase
{
    /** @test */
    public function it_can_parse_simple_image_wikitext()
    {
        $image = new WikitextImage('[[File:Name.jpg]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), null);
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), null);
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), null);
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), null);
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), null);
    }

    /** @test */
    public function it_can_parse_image_wikitext_with_few_params()
    {
        $image = new WikitextImage('[[File:Name.jpg|thumb|left|200px]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), null);
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), null);
    }

    /** @test */
    public function params_can_be_mixed_in_any_order()
    {
        $image = new WikitextImage('[[File:Name.jpg|left|thumbnail=foo|border|upright|lang=foo|text-bottom|alt=foo|link=foo|Image Caption]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumbnail=foo');
        $this->assertSame($image->getBorder(), 'border');
        $this->assertSame($image->getLocation(), 'left');
        $this->assertSame($image->getAlignment(), 'text-bottom');
        $this->assertSame($image->getSize(), 'upright');
        $this->assertSame($image->getLink(), 'link=foo');
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), 'lang=foo');
        $this->assertSame($image->getCaption(), 'Image Caption');
    }

    /** @test */
    public function caption_is_sanitized_against_formatting_links_and_templates()
    {
        $image = new WikitextImage("[[File:Name.jpg|right|frame|x200px|alt=foo|Image caption with [[Url|Link]] and {{nobr|Template with [[Another Link]]}} and '''Formatting with q'otes'''!");

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'frame');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'right');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), 'x200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), "Image caption with Link and Template with Another Link and Formatting with q'otes!");
    }

    /** @test */
    public function it_ignores_parts_with_unknown_parameters()
    {
        $image = new WikitextImage('[[File:Name.jpg|none|thumb=foo|100x200px|super|alt=foo|foo=bar|Image Caption|page=11]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb=foo');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'none');
        $this->assertSame($image->getAlignment(), 'super');
        $this->assertSame($image->getSize(), '100x200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=foo');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), 'Image Caption');
    }

    /** @test */
    public function it_has_get_description_method_which_returns_caption_by_default()
    {
        $image = new WikitextImage('[[File:Name.jpg|alt=Image Alt|Image Caption]]');
        $this->assertSame($image->getDescription(), 'Image Caption');
    }

    /** @test */
    public function which_will_return_alt_if_caption_is_empty()
    {
        $image = new WikitextImage('[[File:Name.jpg|alt=Image Alt]]');
        $this->assertSame($image->getDescription(), 'Image Alt');
    }

    /** @test */
    public function which_will_return_null_if_caption_and_alt_are_both_empty()
    {
        $image = new WikitextImage('[[File:Name.jpg]]');
        $this->assertSame($image->getDescription(), null);
    }

    /** @test */
    public function it_can_handle_ru_specific_wikitext_params_and_converts_them_to_en()
    {
        $image = new WikitextImage('[[Файл:Name.jpg|мини|справа|200пкс|альт=Альтернативный текст|Описание картинки]]');

        $this->assertSame($image->getName(), 'File:Name.jpg');
        $this->assertSame($image->getType(), 'thumb');
        $this->assertSame($image->getBorder(), null);
        $this->assertSame($image->getLocation(), 'right');
        $this->assertSame($image->getAlignment(), null);
        $this->assertSame($image->getSize(), '200px');
        $this->assertSame($image->getLink(), null);
        $this->assertSame($image->getAlt(), 'alt=Альтернативный текст');
        $this->assertSame($image->getLangtag(), null);
        $this->assertSame($image->getCaption(), 'Описание картинки');
    }
}
