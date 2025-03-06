<?php

namespace Illuminated\Wikipedia\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Wikipedia;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;

class PageTest extends TestCase
{
    #[Test]
    public function it_can_be_retrieved_by_title(): void
    {
        $page = (new Wikipedia('ru'))->page('Пушкин');

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    #[Test]
    public function it_can_be_retrieved_by_id_if_integer_passed(): void
    {
        $page = (new Wikipedia('ru'))->page(537);

        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertEquals(537, $page->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $page->getTitle());
    }

    #[Test]
    public function some_can_be_marked_as_missed(): void
    {
        $page = (new Wikipedia)->page('Fake-Not-Existing-Page');

        $this->assertTrue($page->isMissing());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isInvalid());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
        $this->assertEquals('The page `Fake-Not-Existing-Page` does not exist.', $page);
        $this->assertEquals('The page `Fake-Not-Existing-Page` does not exist.', $page->getBody());
    }

    #[Test]
    public function some_can_be_marked_as_invalid(): void
    {
        $page = (new Wikipedia)->page('Talk:');

        $this->assertTrue($page->isInvalid());
        $this->assertFalse($page->isSuccess());
        $this->assertFalse($page->isDisambiguation());
        $this->assertFalse($page->isMissing());
        $this->assertNull($page->getId());
        $this->assertNull($page->getTitle());
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only a namespace prefix.', $page);
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only a namespace prefix.', $page->getBody());
    }

    #[Test]
    public function some_can_be_marked_as_disambiguation(): void
    {
        $page = (new Wikipedia)->page('David Taylor');

        $this->assertTrue($page->isDisambiguation());
        $this->assertTrue($page->isSuccess());
        $this->assertFalse($page->isInvalid());
        $this->assertFalse($page->isMissing());
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function it_is_returned_in_specified_in_config_format_by_default(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bulma'])->andReturn('foo');

        (new Wikipedia)->page('Mocked Page')->getBody();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function but_you_can_use_plain_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['plain'])->andReturn('foo');

        (new Wikipedia)->page('Mocked Page')->plain();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function there_is_also_bulma_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bulma'])->andReturn('foo');

        (new Wikipedia)->page('Mocked Page')->bulma();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function there_is_also_bootstrap_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bootstrap'])->andReturn('foo');

        (new Wikipedia)->page('Mocked Page')->bootstrap();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function custom_section_can_be_appended_to_the_page_and_default_level_is_2(): void
    {
        $this->mockWikipediaQuery();

        $sections = (new Wikipedia)->page('Mocked Page')
            ->append('Appended title', 'Appended body')
            ->getSections();

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title', 'Appended body', 2),
        ]), $sections);
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function however_level_of_appended_section_can_be_set_manually(): void
    {
        $this->mockWikipediaQuery();

        $sections = (new Wikipedia)->page('Mocked Page')
            ->append('Appended title', 'Appended body', 5)
            ->getSections();

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title', 'Appended body', 5),
        ]), $sections);
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function page_sections_can_be_massaged_in_any_way_through_the_get_sections_method(): void
    {
        $this->mockWikipediaQuery();

        $page = (new Wikipedia)->page('Mocked Page');
        $page->getSections()
            ->push(new Section('Appended title 1', 'Appended body 1', 2))
            ->push(new Section('Appended title 1.1', 'Appended body 1.1', 3))
            ->push(new Section('Appended title 1.2', 'Appended body 1.2', 3));

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title 1', 'Appended body 1', 2),
            new Section('Appended title 1.1', 'Appended body 1.1', 3),
            new Section('Appended title 1.2', 'Appended body 1.2', 3),
        ]), $page->getSections());
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function mocked_page_test_with_images_enabled_but_page_does_not_have_any_images(): void
    {
        $this->mockWikipediaQuery();
        config(['wikipedia-grabber.images' => true]);

        $page = (new Wikipedia)->page('Mocked Page');

        $this->assertTrue($page->isSuccess());
        $this->assertEquals('Mocked Page', $page->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PageTest/mocked-page-without-images.txt')),
            trim($page->plain())
        );
    }

    #[Test]
    public function real_page_test_with_images_enabled_but_page_does_not_have_any(): void
    {
        config(['wikipedia-grabber.images' => true]);

        $page = (new Wikipedia('ru'))->page('Иванов, Иван Иванович (священник)');

        $this->assertTrue($page->isSuccess());
        $this->assertEquals('Иванов, Иван Иванович (священник)', $page->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PageTest/page-without-images.txt')),
            trim($page->plain())
        );
    }

    #[Test]
    public function real_page_test_with_images_enabled_and_page_has_them(): void
    {
        config(['wikipedia-grabber.images' => true]);

        $page = (new Wikipedia)->page('Table_(furniture)');

        $this->assertTrue($page->isSuccess());
        $this->assertEquals('Table (furniture)', $page->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PageTest/page-with-images-when-enabled.txt')),
            trim($page->plain())
        );
    }

    #[Test]
    public function real_page_test_with_images_disabled_and_page_has_them(): void
    {
        config(['wikipedia-grabber.images' => false]);

        $page = (new Wikipedia)->page('Table_(furniture)');

        $this->assertTrue($page->isSuccess());
        $this->assertEquals('Table (furniture)', $page->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PageTest/page-with-images-when-disabled.txt')),
            trim($page->plain())
        );
    }
}
