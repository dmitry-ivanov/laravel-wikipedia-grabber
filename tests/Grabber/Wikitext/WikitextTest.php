<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;
use Illuminated\Wikipedia\Tests\TestCase;

class WikitextTest extends TestCase
{
    /** @test */
    public function it_has_plain_method_for_converting_wikitext_to_plain_text()
    {
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline.sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->plain());
    }

    /** @test */
    public function it_can_remove_formatting_from_wikitext()
    {
        $this->assertEquals(
            "Some formatted text - and this text doesn't care about it.",
            (new Wikitext("''Some formatted text'' - and this text doesn't care about it."))->removeFormatting()
        );
    }

    /** @test */
    public function it_works_fine_with_wikitext_without_formatting()
    {
        $this->assertEquals(
            "Some not formatted text - doesn't remove single quote.",
            (new Wikitext("Some not formatted text - doesn't remove single quote."))->removeFormatting()
        );
    }

    /** @test */
    public function it_works_fine_with_wikitext_with_few_formatting()
    {
        $this->assertEquals(
            "Doesn't remove quote here and more and more with q'oute.",
            (new Wikitext("''Doesn't remove quote here'' and '''more''' and '''''more with q'oute'''''."))->removeFormatting()
        );
    }

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
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.links.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline.links.sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->removeLinks());
    }

    /** @test */
    public function which_works_with_file_blocks_inside()
    {
        $this->assertEquals(
            'Wikitext with  File block!',
            (new Wikitext('Wikitext with [[File:Test.jpg|thumb|Simple File]] File block!'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_with_mixed_links_and_file_blocks_inside()
    {
        $this->assertEquals(
            'This is Link1, and this is  file skipped, and Link3',
            (new Wikitext('This is [[Super Link|Link1]], and this is [[File:Test.jpg|thumb|Alt=Some [[Link]] in alt!]] file skipped, and [[Link3]]'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_with_mixed_links_and_few_file_blocks_inside()
    {
        $this->assertEquals(
            'This is Link1, and this is  file skipped  twice, and Link3',
            (new Wikitext('This is [[Super Link|Link1]], and this is [[File:Test.jpg|thumb|Alt=Some [[Link]] in alt!]] file skipped [[File:Test2.jpg|thumb|Alt=Some [[Link2]] in alt2!]] twice, and [[Link3]]'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_with_file_in_file_cases()
    {
        $this->assertEquals(
            '/!! IWG-FILE-TITLE !!/|thumb|File with Link and  File Block!]]',
            (new Wikitext('/!! IWG-FILE-TITLE !!/|thumb|File with [[Link]] and [[File:Inner.jpg|thumb|Some [[Link]] here too]] File Block!]]'))->removeLinks()
        );
    }

    /** @test */
    public function which_works_with_file_in_file_cases_2()
    {
        $this->assertEquals(
            '[[File:Test.jpg|thumb|File with Link and /!! IWG-FILE-TITLE !!/|thumb|Some Link here too]] File Block!]]',
            (new Wikitext('[[File:Test.jpg|thumb|File with [[Link]] and /!! IWG-FILE-TITLE !!/|thumb|Some [[Link]] here too]] File Block!]]'))->removeLinks()
        );
    }

    /** @test */
    public function it_can_remove_templates_from_wikitext()
    {
        $this->assertEquals(
            ' Some Text',
            (new Wikitext('{{nobr|Some Text}}'))->removeTemplates()
        );
    }

    /** @test */
    public function it_has_an_optional_param_to_pass_wikitext_body_into_the_remove_templates_method()
    {
        $this->assertEquals(
            ' Some Passed Text',
            (new Wikitext('{{nobr|Some Text}}'))->removeTemplates('{{nobr|Some Passed Text}}')
        );
    }

    /** @test */
    public function which_works_for_simple_templates_too()
    {
        $this->assertEquals(
            ' simple',
            (new Wikitext('{{simple}}'))->removeTemplates()
        );
    }

    /** @test */
    public function which_works_for_multiple_templates_too()
    {
        $this->assertEquals(
            'This is Template1, and this is Template2, and foo!',
            (new Wikitext('This is {{nobr|Template1}}, and this is {{nowrap|Template2}}, and {{foo}}!'))->removeTemplates()
        );
    }

    /** @test */
    public function there_are_special_nbsp_and_space_templates_which_are_replaced_by_single_space()
    {
        $this->assertEquals(
            'An example of wikitext with space templates.',
            (new Wikitext('An example{{nbsp}}of wikitext{{space}}with{{space|em}}space{{spaces|10}}templates.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_are_special_clear_templates_which_are_replaced_by_single_space()
    {
        $this->assertEquals(
            'An example of wikitext with clear templates.',
            (new Wikitext('An example{{clear}}of wikitext{{clr}}with{{-}}clear{{clear|left}}templates.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_is_special_sfn_template_which_is_ignored()
    {
        $this->assertEquals(
            'An example of wikitext with sfn template.',
            (new Wikitext('An example of wikitext with sfn{{sfn|Roberts|2014|p=3}} template.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_is_special_cite_template_which_is_ignored()
    {
        $this->assertEquals(
            'An example of wikitext with cite template.',
            (new Wikitext('An example of wikitext with cite{{cite web|url=http://example.com|title=Madonna|lang=en}} template.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_is_special_section_link_template_which_is_ignored()
    {
        $this->assertEquals(
            'An example of wikitext with section link template.',
            (new Wikitext('An example of wikitext with section link{{section link|url=http://example.com|title=Madonna|lang=en}} template.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_are_special_anchor_templates_which_are_ignored()
    {
        $this->assertEquals(
            'Wikitext with anchor templates.',
            (new Wikitext('Wikitext with anchor{{anchor|1|2|3}} templates{{якорь|1|2|3}}.'))->removeTemplates()
        );
    }

    /** @test */
    public function there_are_special_see_above_templates_which_are_ignored()
    {
        $this->assertEquals(
            'Wikitext with see above templates.',
            (new Wikitext('Wikitext{{see above|1|2|3}} with{{above|1|2|3}} see{{see at|1|2|3}} above{{см. выше|1|2|3}} templates{{выше|1|2|3}}.{{переход|1|2|3}}'))->removeTemplates()
        );
    }

    /** @test */
    public function there_are_special_see_below_templates_which_are_ignored()
    {
        $this->assertEquals(
            'Wikitext with see below templates.',
            (new Wikitext('Wikitext{{see below|1|2|3}} with{{below|1|2|3}} see{{см. ниже|1|2|3}} below{{ниже|1|2|3}} templates.'))->removeTemplates()
        );
    }

    /** @test */
    public function templates_can_be_in_any_case()
    {
        $this->assertEquals(
            'Wikitext with templates with mixed case.',
            (new Wikitext('Wikitext with{{See Below|1|2|3}} templates{{СМ. Ниже|1|2|3}} with{{SEE AT|1|2|3}} mixed{{NBSP}}case.'))->removeTemplates()
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
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.templates.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline.templates.sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->removeTemplates());
    }

    /** @test */
    public function it_can_remove_html_tags_from_wikitext()
    {
        $this->assertEquals(
            'Some text with html tags!',
            (new Wikitext('Some text<ref>some ref text</ref> with<ref name="G23"/> <b>html tags</b>!'))->removeHtmlTags()
        );
    }

    /** @test */
    public function it_can_remove_html_tags_with_attributes_from_wikitext()
    {
        $this->assertEquals(
            'Another text with tags!',
            (new Wikitext('Another<ref name="G23"/> text<ref with="attributes" more="attributes">some ref text</ref> with<br><p>tags</p>!'))->removeHtmlTags()
        );
    }

    /** @test */
    public function which_works_for_multiple_html_tags_too()
    {
        $this->assertEquals(
            'Multiple html tags!',
            (new Wikitext(''))->removeHtmlTags('Multiple <s foo="bar" baz>html</s> tags<ref>ignored</ref>!<ref with="attr">ignored</ref>')
        );
    }

    /** @test */
    public function which_works_for_wikitext_without_html_tags_too()
    {
        $this->assertEquals(
            'This is wikitext without html tags',
            (new Wikitext('This is wikitext without html tags'))->removeHtmlTags()
        );
    }

    /** @test */
    public function which_works_for_multiline_wikitext_with_html_tags_too()
    {
        $multiline = file_get_contents(__DIR__ . '/WikitextTest/multiline.html.txt');
        $sanitized = file_get_contents(__DIR__ . '/WikitextTest/multiline.html.sanitized.txt');

        $this->assertEquals($sanitized, (new Wikitext($multiline))->removeHtmlTags());
    }
}
