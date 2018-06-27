<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\LocaleFile;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

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
