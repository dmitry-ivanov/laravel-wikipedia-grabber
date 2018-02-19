<?php

namespace Illuminated\Wikipedia\Grabber;

class Page extends Target
{
    protected $response;

    protected function grab()
    {
        $this->response = json_decode(
            $this->client->get('', $this->params())->getBody(),
            true
        );
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts
     */
    protected function params()
    {
        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => 'extracts',
                'exlimit' => 1,
            ], $this->targetParams()),
        ];
    }
}
