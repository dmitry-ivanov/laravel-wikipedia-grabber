<?php

namespace Illuminated\Wikipedia\Tests\Grabber;

use Illuminated\Wikipedia\Tests\TestCase;
use Illuminated\Wikipedia\Wikipedia;
use PHPUnit\Framework\Attributes\Test;

class RandomTest extends TestCase
{
    #[Test]
    public function it_can_grab_random_page_for_en(): void
    {
        $page = (new Wikipedia)->randomPage();
        $this->assertTrue($page->isSuccess());
    }

    #[Test]
    public function it_can_grab_random_page_for_ru(): void
    {
        $page = (new Wikipedia('ru'))->randomPage();
        $this->assertTrue($page->isSuccess());
    }

    #[Test]
    public function it_can_grab_random_preview_for_en(): void
    {
        $preview = (new Wikipedia)->randomPreview();
        $this->assertTrue($preview->isSuccess());
    }

    #[Test]
    public function it_can_grab_random_preview_for_ru(): void
    {
        $preview = (new Wikipedia('ru'))->randomPreview();
        $this->assertTrue($preview->isSuccess());
    }
}
