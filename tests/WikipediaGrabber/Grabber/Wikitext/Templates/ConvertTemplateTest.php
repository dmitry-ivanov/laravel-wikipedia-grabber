<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ConvertTemplate;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class ConvertTemplateTest extends TestCase
{
    /** @test */
    public function it_has_extract_method_which_handles_simple_cases()
    {
        $this->assertEquals(
            '2 kilometers',
            (new ConvertTemplate('{{ convert | 2 | km | mi }}'))->extract()
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_params()
    {
        $this->assertEquals(
            '4 feet',
            (new ConvertTemplate('{{convert|4|ft|adj=mid|-long}}'))->extract()
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_precision()
    {
        $this->assertEquals(
            '5 feet',
            (new ConvertTemplate('{{convert|5|ft|0|adj=mid|-long}}'))->extract()
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_range()
    {
        $this->assertEquals(
            '137 - 156 centimeters',
            (new ConvertTemplate('{{convert|137|-|156|cm|hand in|abbr=h}}'))->extract()
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_range_2()
    {
        $this->assertEquals(
            '137 or 156 centimeters',
            (new ConvertTemplate('{{convert|137|or|156|cm|hand in|abbr=on}}'))->extract()
        );
    }

    /** @test */
    public function it_can_handle_case_with_unknown_unit()
    {
        $this->assertEquals(
            '137 abracadabras',
            (new ConvertTemplate('{{convert|137|abracadabra|foo}}'))->extract()
        );
    }
}
