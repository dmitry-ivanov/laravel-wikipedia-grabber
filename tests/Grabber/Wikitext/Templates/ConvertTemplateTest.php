<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ConvertTemplate;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ConvertTemplateTest extends TestCase
{
    #[Test]
    public function it_has_extract_method_which_handles_simple_cases(): void
    {
        $this->assertEquals(
            '2 kilometers',
            (new ConvertTemplate('{{ convert | 2 | km | mi }}'))->extract()
        );
    }

    #[Test]
    public function and_it_can_handle_case_with_params(): void
    {
        $this->assertEquals(
            '4 feet',
            (new ConvertTemplate('{{convert|4|ft|adj=mid|-long}}'))->extract()
        );
    }

    #[Test]
    public function and_it_can_handle_case_with_precision(): void
    {
        $this->assertEquals(
            '5 feet',
            (new ConvertTemplate('{{convert|5|ft|0|adj=mid|-long}}'))->extract()
        );
    }

    #[Test]
    public function and_it_can_handle_case_with_range(): void
    {
        $this->assertEquals(
            '137 - 156 centimeters',
            (new ConvertTemplate('{{convert|137|-|156|cm|hand in|abbr=h}}'))->extract()
        );
    }

    #[Test]
    public function and_it_can_handle_case_with_range_2(): void
    {
        $this->assertEquals(
            '137 or 156 centimeters',
            (new ConvertTemplate('{{convert|137|or|156|cm|hand in|abbr=on}}'))->extract()
        );
    }

    #[Test]
    public function it_can_handle_case_with_unknown_unit(): void
    {
        $this->assertEquals(
            '137 abracadabras',
            (new ConvertTemplate('{{convert|137|abracadabra|foo}}'))->extract()
        );
    }
}
