<?php

namespace Illuminated\Wikipedia\Tests\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineFile;

class MultilineFileTest extends TestCase
{
    /** @test */
    public function it_has_flatten_method_which_flattens_multiline_files()
    {
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/MultilineFileTest/flatten.txt')),
            (new MultilineFile)->flatten(
                trim(file_get_contents(__DIR__ . '/MultilineFileTest/body.txt'))
            )
        );
    }
}
