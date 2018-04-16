<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests;

use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\MediaWiki;

class MediaWikiTest extends TestCase
{
    /** @test */
    public function it_requires_url_while_initialization()
    {
        $wiki = new MediaWiki('https://en.wikipedia.org/w/api.php');

        $this->assertEquals(
            'https://en.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    /** @test */
    public function which_can_be_url_to_any_locale_mediawiki()
    {
        $wiki = new MediaWiki('https://ru.wikipedia.org/w/api.php');

        $this->assertEquals(
            'https://ru.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_returns_the_same_page_object_as_wikipedia_class_with_the_same_functionality()
    {
        $this->mockWikipediaQuery();

        $page = (new MediaWiki('https://ru.wikipedia.org/w/api.php'))->page('Пушкин');

        $this->assertInstanceOf(Page::class, $page);
    }
}
