<?php

namespace Illuminated\Wikipedia\Tests;

use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\MediaWiki;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;

class MediaWikiTest extends TestCase
{
    #[Test]
    public function it_requires_url_while_initialization(): void
    {
        $wiki = new MediaWiki('https://en.wikipedia.org/w/api.php');

        $this->assertEquals(
            'https://en.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    #[Test]
    public function which_can_be_url_to_any_locale_mediawiki(): void
    {
        $wiki = new MediaWiki('https://ru.wikipedia.org/w/api.php');

        $this->assertEquals(
            'https://ru.wikipedia.org/w/api.php',
            (string) $wiki->getClient()->getConfig('base_uri')
        );
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function it_returns_the_same_page_object_as_wikipedia_class_with_the_same_functionality(): void
    {
        $this->mockWikipediaQuery();

        $page = (new MediaWiki('https://en.wikipedia.org/w/api.php'))->page('Mocked Page');

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf(Page::class, $page);
    }
}
