<?php

namespace Illuminated\Wikipedia\Grabber;

trait PageGrabbing
{
    public function page($title)
    {
        $response = $this->client->get('', $this->pageParams($title));

        return json_decode($response->getBody(), true);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts
     */
    protected function pageParams($title)
    {
        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => 'extracts',
                'exlimit' => 1,
            ], $this->targetParams($title)),
        ];
    }
}
