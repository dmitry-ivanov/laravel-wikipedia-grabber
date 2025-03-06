<?php

namespace Illuminated\Wikipedia\Tests;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Wikipedia;
use PHPUnit\Framework\Attributes\Test;

class GrabberTest extends TestCase
{
    #[Test]
    public function it_has_get_client_method(): void
    {
        $client = (new Wikipedia)->getClient();

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf(Client::class, $client);
    }

    #[Test]
    public function client_has_default_user_agent_logic(): void
    {
        config([
            'wikipedia-grabber.user_agent' => null,
            'app.name' => 'Laravel Wikipedia Grabber',
            'app.url' => 'https://github.com/dmitry-ivanov/laravel-wikipedia-grabber',
        ]);

        $client = (new Wikipedia)->getClient();

        $this->assertEquals(
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber)',
            $client->getConfig('headers')['User-Agent'],
        );
    }

    #[Test]
    public function and_it_takes_specified_user_agent_if_set(): void
    {
        $client = (new Wikipedia)->getClient();

        $this->assertEquals(
            'Laravel Wikipedia Grabber (https://github.com/dmitry-ivanov/laravel-wikipedia-grabber; dmitry.g.ivanov@gmail.com)',
            $client->getConfig('headers')['User-Agent'],
        );
    }
}
