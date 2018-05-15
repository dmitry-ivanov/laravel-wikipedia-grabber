<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class WikitextTest extends TestCase
{
    /** @test */
    public function it_can_remove_links_from_wikitext()
    {
        $this->assertEquals(
            'Link Title',
            (new Wikitext('[[Link Href|Link Title]]'))->removeLinks()
        );
    }

    /** @test */
    public function it_has_an_optional_param_to_pass_wikitext_body_into_the_method()
    {
        $this->assertEquals(
            'Passed Link Title',
            (new Wikitext('[[Link Href|Link Title]]'))->removeLinks('[[Passed Link Href|Passed Link Title]]')
        );
    }

    /** @test */
    public function which_works_for_simple_links_too()
    {
        $this->assertEquals(
            'Some link',
            (new Wikitext('[[Some link]]'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_for_multiple_links_too()
    {
        $this->assertEquals(
            'This is Link1, and this is Link2, and Link3',
            (new Wikitext('This is [[Super Link|Link1]], and this is [[Wow Link|Link2]], and [[Link3]]'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_for_wikitext_without_links_too()
    {
        $this->assertEquals(
            'This is wikitext without links',
            (new Wikitext('This is wikitext without links'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_for_multiline_wikitext_too()
    {
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline_sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->removeLinks());
    }

    /** @test */
    public function it_can_remove_templates_from_wikitext()
    {
        $this->assertEquals(
            'Some Text',
            (new Wikitext('{{nobr|Some Text}}'))->removeTemplates()
        );
    }

    /** @test */
    public function it_has_an_optional_param_to_pass_wikitext_body_into_the_remove_templates_method()
    {
        $this->assertEquals(
            'Some Passed Text',
            (new Wikitext('{{nobr|Some Text}}'))->removeTemplates('{{nobr|Some Passed Text}}')
        );
    }

    /** @test */
    public function which_works_for_simple_templates_too()
    {
        $this->assertEquals(
            '',
            (new Wikitext('{{simple}}'))->removeTemplates()
        );
    }

    /** @test */
    public function which_works_for_multiple_templates_too()
    {
        $this->assertEquals(
            'This is Template1, and this is Template2, and !',
            (new Wikitext('This is {{nobr|Template1}}, and this is {{nowrap|Template2}}, and {{foo}}!'))->removeTemplates()
        );
    }

    /** @test */
    public function which_works_for_wikitext_without_templates_too()
    {
        $this->assertEquals(
            'This is wikitext without templates',
            (new Wikitext('This is wikitext without templates'))->removeTemplates()
        );
    }

    /** @test */
    public function which_works_for_multiline_wikitext_with_templates_too()
    {
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline_templates.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline_templates_sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->removeTemplates());
    }

    /** @test */
    public function it_can_remove_formatting_from_wikitext()
    {
        $this->assertEquals(
            'Some formatted text',
            (new Wikitext("''Some formatted text''"))->removeFormatting()
        );
    }

    /** @test */
    public function it_works_fine_with_wikitext_without_formatting()
    {
        $this->assertEquals(
            'Some not formatted text',
            (new Wikitext('Some not formatted text'))->removeFormatting()
        );
    }

    /** @test */
    public function it_works_fine_with_wikitext_with_few_formattings()
    {
        $this->assertEquals(
            'Some formatted text and more',
            (new Wikitext("''Some formatted text'' and '''more'''"))->removeFormatting()
        );
    }
}
