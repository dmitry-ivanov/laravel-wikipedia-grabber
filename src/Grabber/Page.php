<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Collection;

class Page extends EntitySingular
{
    /**
     * Grab the content.
     */
    protected function grab(): void
    {
        $this->response = head($this->request($this->params())['query']['pages']);

        if ($this->isSuccess() && $this->withImages) {
            $this->response['iwg_wikitext'] = head($this->response['revisions'])['content'];
            $this->response['iwg_main_image'] = $this->getMainImage();
            $this->response['iwg_images_info'] = $this->getImagesInfo();
        }
    }

    /**
     * Get the main image.
     */
    protected function getMainImage(): array|null
    {
        if (empty($this->response['original']) || empty($this->response['thumbnail'])) {
            return null;
        }

        return [
            'original' => $this->response['original'],
            'thumbnail' => $this->response['thumbnail'],
        ];
    }

    /**
     * Get images info.
     */
    protected function getImagesInfo(): array
    {
        if (empty($this->response['images'])) {
            return [];
        }

        return collect($this->response['images'])->pluck('title')->chunk(50)->map(function ($chunk) {
            return $this->request($this->imageInfoParams($chunk))['query']['pages'];
        })->collapse()->toArray();
    }

    /**
     * Get the params.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageprops - Disambiguation
     * @see https://en.wikipedia.org/w/api.php?action=query&list=pagepropnames&titles=MediaWiki - List of pageprops
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+extracts - Contents of the page
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+revisions - Wikitext for images
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageimages - Main image
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+images - All images list
     */
    protected function params(): array
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
            $params->put('piprop', 'thumbnail|original');
            $params->put('pithumbsize', $this->imageSize + 50);

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
     * Get params for getting image info.
     *
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+imageinfo
     */
    protected function imageInfoParams(Collection $images): array
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
