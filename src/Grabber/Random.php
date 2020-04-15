<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

class Random
{
    /**
     * The client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new instance of the Random page.
     *
     * @param \GuzzleHttp\Client $client
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function title()
    {
        $response = head($this->request($this->params())['query']['random']);

        return $response['title'];
    }

    /**
     * Get the params.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+random - Random
     *
     * @return array
     */
    protected function params()
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
     *
     * @param array $params
     * @return array
     */
    protected function request(array $params)
    {
        return json_decode(
            $this->client->get('', $params)->getBody(),
            true
        );
    }
}
