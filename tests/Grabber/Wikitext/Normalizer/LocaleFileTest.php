<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\LocaleFile;

class LocaleFileTest extends TestCase
{
    /** @test */
    public function it_has_normalize_method_which_handles_locale_files()
    {
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/LocaleFileTest/normalize.txt')),
            (new LocaleFile)->normalize(
                trim(file_get_contents(__DIR__ . '/LocaleFileTest/body.txt'))
            )
        );
    }
}
