<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\Underscores;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UnderscoresTest extends TestCase
{
    #[Test]
    public function it_has_normalize_method_which_replace_underscores_to_spaces(): void
    {
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/UnderscoresTest/normalize.txt')),
            (new Underscores)->normalize(
                trim(file_get_contents(__DIR__ . '/UnderscoresTest/body.txt'))
            )
        );
    }
}
