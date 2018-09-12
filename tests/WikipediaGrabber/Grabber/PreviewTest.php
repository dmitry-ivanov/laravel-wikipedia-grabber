<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Wikipedia;
use Illuminated\Wikipedia\Grabber\Preview;
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
    public function there_is_also_bootstrap_helper_method_to_change_format_on_the_fly()
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects()->parse('bootstrap');

        (new Wikipedia)->preview('Mocked Page')->bootstrap();
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
            trim(file_get_contents(__DIR__ . '/PreviewTest/preview-with-images-when-enabled.txt')),
            trim($preview->plain())
        );
    }

    /** @test */
    public function real_preview_test_with_images_disabled_and_preview_has_them()
    {
        config(['wikipedia-grabber.images' => false]);

        $preview = (new Wikipedia)->preview('Table_(furniture)');

        $this->assertTrue($preview->isSuccess());
        $this->assertEquals('Table (furniture)', $preview->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PreviewTest/preview-with-images-when-disabled.txt')),
            trim($preview->plain())
        );
    }
}
