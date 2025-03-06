<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\LocaleFile;
use Illuminated\Wikipedia\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LocaleFileTest extends TestCase
{
    #[Test]
    public function it_has_normalize_method_which_handles_locale_files(): void
    {
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/LocaleFileTest/normalize.txt')),
            (new LocaleFile)->normalize(
                trim(file_get_contents(__DIR__ . '/LocaleFileTest/body.txt'))
            )
        );
    }
}
