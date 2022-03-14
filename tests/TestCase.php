<?php

namespace Illuminated\Wikipedia\Tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Illuminated\Wikipedia\WikipediaGrabberServiceProvider;
use Mockery;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [WikipediaGrabberServiceProvider::class];
    }

    /**
     * Resolve application core configuration implementation.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function resolveApplicationConfiguration($app)
    {
        $fixturePath = __DIR__ . '/fixture/config/wikipedia-grabber.php';
        $orchestraPath = $this->getBasePath() . '/config/wikipedia-grabber.php';
        copy($fixturePath, $orchestraPath);

        parent::resolveApplicationConfiguration($app);

        unlink($orchestraPath);
    }

    /**
     * Mock Wikipedia query.
     */
    protected function mockWikipediaQuery(): void
    {
        $body = json_encode([
            'query' => [
                'pages' => [[
                    'pageid' => 1234567,
                    'title' => 'Mocked Page',
                    'extract' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'revisions' => [[
                        'contentformat' => 'text/x-wiki',
                        'contentmodel' => 'wikitext',
                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    ]],
                ]],
            ],
        ]);
        $response = new Response(200, ['Content-Type' => 'application/json'], Utils::streamFor($body));

        $client = mock('overload:GuzzleHttp\Client');
        $client->expects('get')->withArgs(['', Mockery::any()])->andReturn($response);
    }
}
