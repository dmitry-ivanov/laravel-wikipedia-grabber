<?php

namespace Illuminated\Wikipedia\Grabber;

class Preview extends EntitySingular
{
    protected function grab()
    {
        $this->response = head($this->request($this->params())['query']['pages']);

        if ($this->isSuccess() && $this->withImages) {
            $this->response['iwg_wikitext'] = '';
            $this->response['iwg_main_image'] = $this->getMainImage();
            $this->response['iwg_images_info'] = [];
        }
    }

    protected function getMainImage()
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
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageprops - Disambiguation
     * @see https://en.wikipedia.org/w/api.php?action=query&list=pagepropnames&titles=MediaWiki - List of pageprops
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+extracts - Contents of the page
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query+pageimages - Main image
     */
    protected function params()
    {
        $prop = collect();
        $params = collect();

        $prop->push('pageprops');
        $params->put('ppprop', 'disambiguation');

        $prop->push('extracts');
        $params->put('exlimit', 1);
        $params->put('exintro', true);
        $params->put('explaintext', true);

        if ($this->withImages) {
            $prop->push('pageimages');
            $params->put('piprop', 'thumbnail|original');
            $params->put('pithumbsize', $this->imageSizeOnPreview);
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
}
