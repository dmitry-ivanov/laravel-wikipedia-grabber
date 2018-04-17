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

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        dd($fullResponse);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $this->response = head($fullResponse['query']['pages']);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+extracts - Extracts: contents of the page
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageprops - PageProps: disambiguation
     * @see https://en.wikipedia.org/w/api.php?action=query&list=pagepropnames&titles=MediaWiki - Avaliable pageprop names
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+revisions - Revisions: wikitext for images
     */
    protected function params()
    {
        $prop = collect(['extracts', 'pageprops']);

        $imageParams = [];
        if (config('wikipedia-grabber.images')) {
            $prop->push('revisions');
            $imageParams = [
                'rvprop' => 'content',
                'rvcontentformat' => 'text/x-wiki',
            ];
        }

        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => $prop->implode('|'),
                'exlimit' => 1,
                'explaintext' => true,
                'exsectionformat' => 'wiki',
                'ppprop' => 'disambiguation',
            ], $this->targetParams(), $imageParams),
        ];
    }
}
