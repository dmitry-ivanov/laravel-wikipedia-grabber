<?php

namespace Illuminated\Wikipedia\Grabber;

class Page
{
    public function __construct()
    {
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts
     */
    protected function params($title)
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

    protected function targetParams($target)
    {
        if (is_int($target)) {
            return ['pageids' => $target];
        }

        return ['titles' => $target];
    }
}
