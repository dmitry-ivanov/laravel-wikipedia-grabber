<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\Wikipedia;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class RandomTest extends TestCase
{
    /** @test */
    public function it_can_grab_random_page_for_en()
    {
        $page = (new Wikipedia)->random();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertTrue($page->isSuccess());
    }

    /** @test */
    public function it_can_grab_random_page_for_ru()
    {
        $page = (new Wikipedia('ru'))->random();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertTrue($page->isSuccess());
    }
}
