<?php

namespace Illuminated\Wikipedia\Tests;

use Mockery;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Illuminated\Wikipedia\WikipediaGrabberServiceProvider;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [WikipediaGrabberServiceProvider::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        $fixturePath = __DIR__ . '/fixture/config/wikipedia-grabber.php';
        $orchestraPath = $this->getBasePath() . '/config/wikipedia-grabber.php';
        copy($fixturePath, $orchestraPath);

        parent::resolveApplicationConfiguration($app);

        unlink($orchestraPath);
    }

    protected function mockWikipediaQuery()
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
        $response = new Response(200, ['Content-Type' => 'application/json'], Psr7\stream_for($body));

        $client = mock('overload:GuzzleHttp\Client');
        $client->expects()->get('', Mockery::any())->andReturn($response);
    }
}
