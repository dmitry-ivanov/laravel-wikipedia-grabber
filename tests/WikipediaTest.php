<?php

namespace Illuminated\Wikipedia\Tests;

use Illuminated\Wikipedia\Wikipedia;
use PHPUnit\Framework\Attributes\Test;

class WikipediaTest extends TestCase
{
    #[Test]
    public function it_is_en_wikipedia_by_default(): void
    {
        $wiki = new Wikipedia;

        $this->assertEquals(
            'https://en.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    #[Test]
    public function but_you_can_set_any_language_while_initializing(): void
    {
        $wiki = new Wikipedia('ru');

        $this->assertEquals(
            'https://ru.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }
}
