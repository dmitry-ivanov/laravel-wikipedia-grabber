<?php

namespace Illuminated\Wikipedia\Tests\Grabber;

use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Wikipedia;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;

class PreviewTest extends TestCase
{
    #[Test]
    public function it_can_be_retrieved_by_title(): void
    {
        $preview = (new Wikipedia('ru'))->preview('Пушкин');

        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertEquals(537, $preview->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $preview->getTitle());
    }

    #[Test]
    public function it_can_be_retrieved_by_id_if_integer_passed(): void
    {
        $preview = (new Wikipedia('ru'))->preview(537);

        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertEquals(537, $preview->getId());
        $this->assertEquals('Пушкин, Александр Сергеевич', $preview->getTitle());
    }

    #[Test]
    public function some_can_be_marked_as_missed(): void
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

    #[Test]
    public function some_can_be_marked_as_invalid(): void
    {
        $preview = (new Wikipedia)->preview('Talk:');

        $this->assertTrue($preview->isInvalid());
        $this->assertFalse($preview->isSuccess());
        $this->assertFalse($preview->isDisambiguation());
        $this->assertFalse($preview->isMissing());
        $this->assertNull($preview->getId());
        $this->assertNull($preview->getTitle());
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only a namespace prefix.', $preview);
        $this->assertEquals('The page `Talk:` is invalid. The requested page title is empty or contains only a namespace prefix.', $preview->getBody());
    }

    #[Test]
    public function some_can_be_marked_as_disambiguation(): void
    {
        $preview = (new Wikipedia)->preview('David Taylor');

        $this->assertTrue($preview->isDisambiguation());
        $this->assertTrue($preview->isSuccess());
        $this->assertFalse($preview->isInvalid());
        $this->assertFalse($preview->isMissing());
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function it_is_returned_in_specified_in_config_format_by_default(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bulma'])->andReturn('foo');

        (new Wikipedia)->preview('Mocked Page')->getBody();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function but_you_can_use_plain_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['plain'])->andReturn('foo');

        (new Wikipedia)->preview('Mocked Page')->plain();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function there_is_also_bulma_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bulma'])->andReturn('foo');

        (new Wikipedia)->preview('Mocked Page')->bulma();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function there_is_also_bootstrap_helper_method_to_change_format_on_the_fly(): void
    {
        $this->mockWikipediaQuery();

        $parser = mock('overload:Illuminated\Wikipedia\Grabber\Parser\Parser');
        $parser->expects('parse')->withArgs(['bootstrap'])->andReturn('foo');

        (new Wikipedia)->preview('Mocked Page')->bootstrap();
    }

    #[Test] #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function mocked_preview_test_with_images_enabled_but_preview_does_not_have_any_images(): void
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

    #[Test]
    public function real_preview_test_with_images_enabled_but_preview_does_not_have_any(): void
    {
        config(['wikipedia-grabber.images' => true]);

        $preview = (new Wikipedia('ru'))->preview('Иванов, Иван Иванович (священник)');

        $this->assertTrue($preview->isSuccess());
        $this->assertEquals('Иванов, Иван Иванович (священник)', $preview->getTitle());
        $this->assertEquals(
            trim(file_get_contents(__DIR__ . '/PreviewTest/preview-without-images.txt')),
            trim($preview->plain())
        );
    }

    #[Test]
    public function real_preview_test_with_images_enabled_and_preview_has_them(): void
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

    #[Test]
    public function real_preview_test_with_images_disabled_and_preview_has_them(): void
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
