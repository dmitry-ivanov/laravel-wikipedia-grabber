<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Collection;

class Page extends EntitySingular
{
    protected function grab()
    {
        $fullResponse = json_decode(
            $this->client->get('', $this->params())->getBody(),
            true
        );

        $this->response = head($fullResponse['query']['pages']);

        if ($this->withImages) {
            $this->response['imagesinfo'] = $this->getImagesInfo();
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $wikitext = head($this->response['revisions'])['content'];

        $mainThumbnail = $this->response['thumbnail'];
        $mainOriginal = $this->response['original'];

        $imagesInfo = $this->response['imagesinfo'];
        dd($mainThumbnail, $mainOriginal, $imagesInfo, $wikitext);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageprops - Disambiguation
     * @see https://en.wikipedia.org/w/api.php?action=query&list=pagepropnames&titles=MediaWiki - List of pageprops
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+extracts - Contents of the page
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+revisions - Wikitext for images
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageimages - Main image
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+images - All images list
     */
    protected function params()
    {
        $prop = collect();
        $params = collect();

        $prop->push('pageprops');
        $params->put('ppprop', 'disambiguation');

        $prop->push('extracts');
        $params->put('exlimit', 1);
        $params->put('explaintext', true);
        $params->put('exsectionformat', 'wiki');

        if ($this->withImages) {
            $prop->push('revisions');
            $params->put('rvprop', 'content');
            $params->put('rvcontentformat', 'text/x-wiki');

            $prop->push('pageimages');
            $params->put('pithumbsize', 300);
            $params->put('piprop', 'thumbnail|original');

            $prop->push('images');
            $params->put('imlimit', 'max');
        }

        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => $prop->implode('|'),
            ], $this->targetParams(), $params->toArray()),
        ];
    }

    protected function getImagesInfo()
    {
        $imagesInfo = collect();

        $images = collect($this->response['images']);
        if ($images->isEmpty()) {
            return $imagesInfo->toArray();
        }

        $images = $images->pluck('title');
        foreach ($images->chunk(50) as $chunk) {
            $fullResponse = json_decode(
                $this->client->get('', $this->imageInfoParams($chunk))->getBody(),
                true
            );

            $imagesInfo->push($fullResponse['query']['pages']);
        }

        return $imagesInfo->collapse()->toArray();
    }

    /**
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+imageinfo
     */
    protected function imageInfoParams(Collection $images)
    {
        return [
            'query' => [
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => 'imageinfo',
                'iiprop' => 'url|mime',
                'iiurlwidth' => 300,
                'iiurlheight' => 300,
                'titles' => $images->implode('|'),
            ],
        ];
    }
}
