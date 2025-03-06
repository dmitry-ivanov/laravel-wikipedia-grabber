<?php

namespace Illuminated\Wikipedia\Tests;

use PHPUnit\Framework\Attributes\Test;

class WikipediaGrabberServiceProviderTest extends TestCase
{
    #[Test]
    public function it_merges_default_configuration_with_published_one(): void
    {
        $this->assertEquals(
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)',
            config('wikipedia-grabber.user_agent')
        );
    }
}
