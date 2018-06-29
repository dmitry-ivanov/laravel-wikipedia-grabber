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
            (new ConvertTemplate('{{ convert | 2 | km | mi }}'))->extract(),
            '2 kilometers'
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_params()
    {
        $this->assertEquals(
            (new ConvertTemplate('{{convert|4|ft|adj=mid|-long}}'))->extract(),
            '4 feet'
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_precision()
    {
        $this->assertEquals(
            (new ConvertTemplate('{{convert|5|ft|0|adj=mid|-long}}'))->extract(),
            '5 feet'
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_range()
    {
        $this->assertEquals(
            (new ConvertTemplate('{{convert|137|-|156|cm|hand in|abbr=h}}'))->extract(),
            '137 - 156 centimeters'
        );
    }

    /** @test */
    public function and_it_can_handle_case_with_range_2()
    {
        $this->assertEquals(
            (new ConvertTemplate('{{convert|137|or|156|cm|hand in|abbr=on}}'))->extract(),
            '137 or 156 centimeters'
        );
    }
}
