<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_merges_default_configuration_with_published_one()
    {
        $config = config('wikipedia-grabber');

        $this->assertEquals(
            $config['user_agent'],
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)'
        );
    }
}
