<?php

namespace Illuminated\Wikipedia\Grabber\Target;

class Page extends Entity
{
    protected $response;

    protected function grab()
    {
        $fullResponse = json_decode(
            $this->client->get('', $this->params())->getBody(),
            true
        );

        $this->response = head($fullResponse['query']['pages']);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Getting_a_list_of_page_IDs                 - FormatVersion
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bextracts             - Extracts
     * @see https://en.wikipedia.org/w/api.php?action=help&modules=query%2Bpageprops            - PageProps
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

    public function isSuccess()
    {
        return !$this->isMissing() && !$this->isInvalid();
    }

    public function isDisambiguation()
    {
        return !empty($this->response['pageprops']) && isset($this->response['pageprops']['disambiguation']);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     */
    public function isMissing()
    {
        return !empty($this->response['missing']);
    }

    /**
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     */
    public function isInvalid()
    {
        return !empty($this->response['invalid']);
    }

    public function getId()
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['pageid'];
    }

    public function getTitle()
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['title'];
    }

    public function getBody()
    {
        if ($this->isMissing()) {
            return $this->getMissingBody();
        }

        if ($this->isInvalid()) {
            return $this->getInvalidBody();
        }

        return $this->response['extract'];
    }

    private function getMissingBody()
    {
        return "The page `{$this->target}` does not exist.";
    }

    private function getInvalidBody()
    {
        $reason = !empty($this->response['invalidreason'])
            ? "\n{$this->response['invalidreason']}"
            : '';

        return "The page `{$this->target}` is invalid.{$reason}";
    }

    public function __toString()
    {
        return $this->getBody();
    }
}