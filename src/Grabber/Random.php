<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

class Random
{
    /**
     * The client.
     */
    protected Client $client;

    /**
     * Create a new instance of the Random page.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the title.
     */
    public function title(): string
    {
        $response = head($this->request($this->params())['query']['random']);

        return $response['title'];
    }

    /**
     * Get the params.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+random - Random
     */
    protected function params(): array
    {
        return [
            'query' => [
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'list' => 'random',
                'rnnamespace' => 0,
                'rnfilterredir' => 'nonredirects',
                'rnlimit' => 1,
            ],
        ];
    }

    /**
     * Make request with the given params.
     */
    protected function request(array $params): array
    {
        return json_decode(
            $this->client->get('', $params)->getBody(),
            true,
        );
    }
}
