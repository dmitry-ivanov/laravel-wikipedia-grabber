<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests;

use Illuminated\Wikipedia\Grabber\Page;
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

    /** @test */
    public function it_has_method_for_page_grabbing()
    {
        $wiki = new Wikipedia;

        $this->assertInstanceOf(Page::class, $wiki->page('Pushkin'));
    }

    /** @test */
    public function page_can_be_retrieved_by_title()
    {
        $page = (new Wikipedia('ru'))->page('Пушкин');

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    /** @test */
    public function or_page_can_be_retrieved_by_id_if_integer_passed()
    {
        $page = (new Wikipedia('ru'))->page(537);

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    /** @test */
    public function some_pages_can_be_marked_as_missed()
    {
        $page = (new Wikipedia)->page('Fake-Unexisting-Page');

        $this->assertTrue($page->isMissing());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isInvalid());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
    }

    /** @test */
    public function some_pages_can_be_marked_as_invalid()
    {
        $page = (new Wikipedia)->page('Talk:');

        $this->assertTrue($page->isInvalid());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isMissing());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
    }

    /** @test */
    public function some_pages_can_be_marked_as_disambiguation()
    {
        $page = (new Wikipedia)->page('David Taylor');

        $this->assertTrue($page->isDisambiguation());
        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isInvalid());
        $this->assertFalse($page->isMissing());
    }
}
