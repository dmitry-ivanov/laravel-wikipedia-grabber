<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Preview;
use Illuminated\Wikipedia\Wikipedia;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class PreviewTest extends TestCase
{
    /** @test */
    public function it_can_be_retrieved_by_title()
    {
        $preview = (new Wikipedia('ru'))->preview('Пушкин');

        $this->assertInstanceOf(Preview::class, $preview);
        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertEquals(537, $preview->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $preview->getTitle());
    }

    /** @test */
    public function it_can_be_retrieved_by_id_if_integer_passed()
    {
        $preview = (new Wikipedia('ru'))->preview(537);

        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertEquals(537, $preview->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $preview->getTitle());
    }

    /** @test */
    public function some_can_be_marked_as_missed()
    {
        $preview = (new Wikipedia)->preview('Fake-Not-Existing-Page');

        $this->assertTrue($preview->isMissing());
        $this->assertFalse($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertFalse($preview->isInvalid());
        $this->assertNull($preview->getId());
        $this->assertNull($preview->getTitle());
        $this->assertEquals('The page `Fake-Not-Existing-Page` does not exist.', $preview);
        $this->assertEquals('The page `Fake-Not-Existing-Page` does not exist.', $preview->getBody());
    }

    /** @test */
    public function some_can_be_marked_as_invalid()
    {
        $preview = (new Wikipedia)->preview('Talk:');

        $this->assertTrue($preview->isInvalid());
        $this->assertFalse($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertFalse($preview->isMissing());
        $this->assertNull($preview->getId());
        $this->assertNull($preview->getTitle());
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only the name of a namespace.', $preview);
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only the name of a namespace.', $preview->getBody());
    }

    /** @test */
    public function some_can_be_marked_as_disambiguation()
    {
        $preview = (new Wikipedia)->preview('David Taylor');

        $this->assertTrue($preview->isDisambiguation());
        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isInvalid());
        $this->assertFalse($preview->isMissing());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_is_returned_in_specified_in_config_format_by_default()
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects()->parse('bulma');

        (new Wikipedia)->preview('Mocked Page')->getBody();
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

        (new Wikipedia)->preview('Mocked Page')->plain();
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

        (new Wikipedia)->preview('Mocked Page')->bulma();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function custom_section_can_be_appended_to_the_preview_and_default_level_is_2()
    {
        $this->mockWikipediaQuery();

        $sections = (new Wikipedia)->preview('Mocked Page')
            ->append('Appended title', 'Appended body')
            ->getSections();

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title', 'Appended body', 2),
        ]), $sections);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function however_level_of_appended_section_can_be_set_manually()
    {
        $this->mockWikipediaQuery();

        $sections = (new Wikipedia)->preview('Mocked Page')
            ->append('Appended title', 'Appended body', 5)
            ->getSections();

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title', 'Appended body', 5),
        ]), $sections);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function preview_sections_can_be_massaged_in_any_way_through_the_get_sections_method()
    {
        $this->mockWikipediaQuery();

        $preview = (new Wikipedia)->preview('Mocked Page');
        $preview->getSections()
            ->push(new Section('Appended title 1', 'Appended body 1', 2))
            ->push(new Section('Appended title 1.1', 'Appended body 1.1', 3))
            ->push(new Section('Appended title 1.2', 'Appended body 1.2', 3));

        $this->assertEquals(collect([
            new Section('Mocked Page', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1),
            new Section('Appended title 1', 'Appended body 1', 2),
            new Section('Appended title 1.1', 'Appended body 1.1', 3),
            new Section('Appended title 1.2', 'Appended body 1.2', 3),
        ]), $preview->getSections());
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function mocked_preview_test_with_images_enabled_but_preview_does_not_have_any_images()
    {
        $this->mockWikipediaQuery();
        config(['wikipedia-grabber.images' => true]);

        $preview = (new Wikipedia)->preview('Mocked Page');

        $this->assertTrue($preview->isSuccess());
        $this->assertEquals('Mocked Page', $preview->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PreviewTest/mocked-preview-without-images.txt')),
            trim($preview->plain())
        );
    }

    /** @test */
    public function real_preview_test_with_images_enabled_but_preview_does_not_have_any()
    {
        config(['wikipedia-grabber.images' => true]);

        $preview = (new Wikipedia('ru'))->preview('Иванов, Иван (богослов)');

        $this->assertTrue($preview->isSuccess());
        $this->assertEquals('Иванов, Иван (богослов)', $preview->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PreviewTest/preview-without-images.txt')),
            trim($preview->plain())
        );
    }

    /** @test */
    public function real_preview_test_with_images_enabled_and_preview_has_them()
    {
        config(['wikipedia-grabber.images' => true]);

        $preview = (new Wikipedia)->preview('Table_(furniture)');

        $this->assertTrue($preview->isSuccess());
        $this->assertEquals('Table (furniture)', $preview->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PreviewTest/preview-with-images.txt')),
            trim($preview->plain())
        );
    }
}
