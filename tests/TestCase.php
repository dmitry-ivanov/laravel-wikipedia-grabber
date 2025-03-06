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
     */
    protected function getPackageProviders($app): array
    {
        return [WikipediaGrabberServiceProvider::class];
    }

    /**
     * Resolve application core configuration implementation.
     */
    protected function resolveApplicationConfiguration($app): void
    {
        $fixturePath = __DIR__ . '/fixture/config/wikipedia-grabber.php';
        $orchestraPath = $this->getApplicationBasePath() . '/config/wikipedia-grabber.php';
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
