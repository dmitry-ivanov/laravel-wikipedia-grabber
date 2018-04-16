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
    public function page_can_be_retrieved_by_title()
    {
        $page = (new Wikipedia('ru'))->page('Пушкин');

        $this->assertInstanceOf(Page::class, $page);
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
        $this->assertEquals('The page `Fake-Unexisting-Page` does not exist.', $page);
        $this->assertEquals('The page `Fake-Unexisting-Page` does not exist.', $page->getBody());
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
        $page = (new Wikipedia)->page('David Taylor');

        $this->assertTrue($page->isDisambiguation());
        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isInvalid());
        $this->assertFalse($page->isMissing());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function page_is_returned_in_specified_in_config_format_by_default()
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects()->parse('bulma');

        (new Wikipedia('ru'))->page('Пушкин')->getBody();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function but_you_can_use_plain_helper_method_to_change_format_on_the_fly()
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects()->parse('plain');

        (new Wikipedia('ru'))->page('Пушкин')->plain();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function there_is_also_bulma_helper_method_to_change_format_on_the_fly()
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects()->parse('bulma');

        (new Wikipedia('ru'))->page('Пушкин')->bulma();
    }
}
