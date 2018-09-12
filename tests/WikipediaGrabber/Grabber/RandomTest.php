<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Wikipedia;
use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\Grabber\Preview;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class RandomTest extends TestCase
{
    /** @test */
    public function it_can_grab_random_page_for_en()
    {
        $page = (new Wikipedia)->randomPage();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertTrue($page->isSuccess());
    }

    /** @test */
    public function it_can_grab_random_page_for_ru()
    {
        $page = (new Wikipedia('ru'))->randomPage();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertTrue($page->isSuccess());
    }

    /** @test */
    public function it_can_grab_random_preview_for_en()
    {
        $preview = (new Wikipedia)->randomPreview();

        $this->assertInstanceOf(Preview::class, $preview);
        $this->assertTrue($preview->isSuccess());
    }

    /** @test */
    public function it_can_grab_random_preview_for_ru()
    {
        $preview = (new Wikipedia('ru'))->randomPreview();

        $this->assertInstanceOf(Preview::class, $preview);
        $this->assertTrue($preview->isSuccess());
    }
}
