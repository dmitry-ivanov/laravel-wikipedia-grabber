<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineTemplate;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MultilineTemplateTest extends TestCase
{
    #[Test]
    public function it_has_flatten_method_which_flattens_multiline_templates(): void
    {
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/MultilineTemplateTest/flatten.txt')),
            (new MultilineTemplate)->flatten(
                trim(file_get_contents(__DIR__ . '/MultilineTemplateTest/body.txt'))
            )
        );
    }
}
