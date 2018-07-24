<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

class Random
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function title()
    {
        $response = head($this->request($this->params())['query']['random']);

        return $response['title'];
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+random - Random
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

    protected function request(array $params)
    {
        return json_decode(
            $this->client->get('', $params)->getBody(),
            true
        );
    }
}
