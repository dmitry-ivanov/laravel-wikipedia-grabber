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

    /** @test */
    public function it_has_method_for_page_grabbing()
    {
        $wiki = new MediaWiki('https://en.wikipedia.org/w/api.php');

        $this->assertInstanceOf(Page::class, $wiki->page('Pushkin'));
    }
}
