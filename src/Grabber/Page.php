<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Collection;

class Page extends EntitySingular
{
    protected function grab()
    {
        $this->response = head($this->request($this->params())['query']['pages']);

        if ($this->isSuccess() && $this->images) {
            $this->response['imagesinfo'] = $this->getImagesInfo();
        }
    }

    protected function getImagesInfo()
    {
        $imagesInfo = collect();

        $images = collect($this->response['images']);
        if ($images->isEmpty()) {
            return [];
        }

        $images = $images->pluck('title');
        foreach ($images->chunk(50) as $chunk) {
            $imagesInfo->push(
                $this->request($this->imageInfoParams($chunk))['query']['pages']
            );
        }

        return $imagesInfo->collapse()->toArray();
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

        if ($this->images) {
            $prop->push('revisions');
            $params->put('rvprop', 'content');
            $params->put('rvcontentformat', 'text/x-wiki');

            $prop->push('pageimages');
            $params->put('pithumbsize', $this->imageSize);
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
                'iiurlwidth' => $this->imageSize,
                'iiurlheight' => $this->imageSize,
                'titles' => $images->implode('|'),
            ],
        ];
    }
}
