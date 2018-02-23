<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests;

use Illuminated\Wikipedia\Grabber\Target\Page;
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

    /** @test */
    public function page_can_be_retrieved_by_title()
    {
        $page = (new MediaWiki('https://ru.wikipedia.org/w/api.php'))->page('Пушкин');

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    /** @test */
    public function or_page_can_be_retrieved_by_id_if_integer_passed()
    {
        $page = (new MediaWiki('https://ru.wikipedia.org/w/api.php'))->page(537);

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    /** @test */
    public function some_pages_can_be_marked_as_missed()
    {
        $page = (new MediaWiki('https://en.wikipedia.org/w/api.php'))->page('Fake-Unexisting-Page');

        $this->assertTrue($page->isMissing());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isInvalid());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
        $this->assertEquals('The page `Fake-Unexisting-Page` does not exist.', $page);
        $this->assertEquals('The page `Fake-Unexisting-Page` does not exist.', $page->getBody());
    }

    /** @test */
    public function some_pages_can_be_marked_as_invalid()
    {
        $page = (new MediaWiki('https://en.wikipedia.org/w/api.php'))->page('Talk:');

        $this->assertTrue($page->isInvalid());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isMissing());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
        $this->assertEquals(
            "The page `Talk:` is invalid.\nThe requested page title is empty or contains only the name of a namespace.",
            $page
        );
        $this->assertEquals(
            "The page `Talk:` is invalid.\nThe requested page title is empty or contains only the name of a namespace.",
            $page->getBody()
        );
    }

    /** @test */
    public function some_pages_can_be_marked_as_disambiguation()
    {
        $page = (new MediaWiki('https://en.wikipedia.org/w/api.php'))->page('David Taylor');

        $this->assertTrue($page->isDisambiguation());
        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isInvalid());
        $this->assertFalse($page->isMissing());
    }
}
