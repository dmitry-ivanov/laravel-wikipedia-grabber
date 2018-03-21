<?php

namespace Illuminated\Wikipedia\Grabber;

class Page extends EntitySingular
{
    protected function grab()
    {
        $fullResponse = json_decode(
            $this->client->get('', $this->params())->getBody(),
            true
        );

        $this->response = head($fullResponse['query']['pages']);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts - Extracts
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bpageprops - PageProps
     * @see https://en.wikipedia.org/w/api.php?action=query&list=pagepropnames&titles=MediaWiki - Avaliable Prop Names
     */
    protected function params()
    {
        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => 'extracts|pageprops',
                'exlimit' => 1,
                'explaintext' => true,
                'exsectionformat' => 'wiki',
                'ppprop' => 'disambiguation',
            ], $this->targetParams()),
        ];
    }
}
