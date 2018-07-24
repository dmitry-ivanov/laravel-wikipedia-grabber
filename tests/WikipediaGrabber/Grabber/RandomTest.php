<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests\Grabber;

use Illuminated\Wikipedia\Grabber\Random;
use Illuminated\Wikipedia\Wikipedia;
use Illuminated\Wikipedia\WikipediaGrabber\Tests\TestCase;

class RandomTest extends TestCase
{
    /** @test */
    public function it_can_generate_random_title_for_en()
    {
        $client = (new Wikipedia)->getClient();

        $title = (new Random($client))->title();

        $this->assertNotEmpty($title);
    }

    /** @test */
    public function it_can_generate_random_title_for_ru()
    {
        $client = (new Wikipedia('ru'))->getClient();

        $title = (new Random($client))->title();

        $this->assertNotEmpty($title);
    }
}
