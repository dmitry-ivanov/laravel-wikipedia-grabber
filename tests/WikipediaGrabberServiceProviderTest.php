<?php

namespace Illuminated\Wikipedia\Tests;

class WikipediaGrabberServiceProviderTest extends TestCase
{
    /** @test */
    public function it_merges_default_configuration_with_published_one()
    {
        $this->assertEquals(
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)',
            config('wikipedia-grabber.user_agent')
        );
    }
}
