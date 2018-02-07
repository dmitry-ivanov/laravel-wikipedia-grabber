<?php

namespace Illuminated\Wikipedia\Grabber;

trait PageGrabbing
{
    public function page($title)
    {
        $response = $this->client->get('', $this->composePageParams($title));

        return json_decode($response->getBody(), true);
    }

    protected function composePageParams($title)
    {
        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2, // https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs
                'redirects' => true,
                'prop' => 'extracts', // https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts
                'exlimit' => 1,
            ], $this->composeTargetParams($title)),
        ];
    }
}
