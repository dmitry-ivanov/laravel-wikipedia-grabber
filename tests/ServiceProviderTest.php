<?php

namespace Illuminated\Wikipedia\Tests;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_merges_default_configuration_with_published_one()
    {
        $this->assertEquals(
            config('wikipedia-grabber.user_agent'),
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)'
        );
    }
}
