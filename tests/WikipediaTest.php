<?php

namespace Illuminated\Wikipedia\Tests;

use Illuminated\Wikipedia\Wikipedia;

class WikipediaTest extends TestCase
{
    /** @test */
    public function it_is_en_wikipedia_by_default()
    {
        $wiki = new Wikipedia;

        $this->assertEquals(
            'https://en.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    /** @test */
    public function but_you_can_set_any_language_while_initializing()
    {
        $wiki = new Wikipedia('ru');

        $this->assertEquals(
            'https://ru.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }
}
