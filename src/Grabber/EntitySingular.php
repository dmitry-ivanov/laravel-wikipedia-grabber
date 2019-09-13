<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Arr;
use Illuminated\Wikipedia\Grabber\Parser\Parser;
use Illuminated\Wikipedia\Grabber\Component\Section;

abstract class EntitySingular extends Entity
{
    protected $parser;
    protected $response;

    public function isSuccess()
    {
        return !$this->isMissing() && !$this->isInvalid();
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

    public function isDisambiguation()
    {
        return !empty($this->response['pageprops']) && isset($this->response['pageprops']['disambiguation']);
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

    public function plain()
    {
        $this->format = 'plain';

        return $this->getBody();
    }

    public function bulma()
    {
        $this->format = 'bulma';

        return $this->getBody();
    }

    public function bootstrap()
    {
        $this->format = 'bootstrap';

        return $this->getBody();
    }

    public function getBody()
    {
        if ($this->isMissing()) {
            return $this->getMissingBody();
        }

        if ($this->isInvalid()) {
            return $this->getInvalidBody();
        }

        return $this->getParser()->parse($this->format);
    }

    protected function getMissingBody()
    {
        return "The page `{$this->target}` does not exist.";
    }

    protected function getInvalidBody()
    {
        $reason = Arr::get($this->response, 'invalidreason');

        return "The page `{$this->target}` is invalid. {$reason}";
    }

    public function append($title, $body, $level = 2)
    {
        $this->getSections()->push(new Section($title, $body, $level));

        return $this;
    }

    public function getSections()
    {
        return $this->getParser()->getSections();
    }

    protected function getParser()
    {
        if (empty($this->parser)) {
            $imagesResponseData = null;
            if ($this->withImages) {
                $imagesResponseData = [
                    'wikitext' => $this->response['iwg_wikitext'],
                    'main_image' => $this->response['iwg_main_image'],
                    'images' => $this->response['iwg_images_info'],
                ];
            }

            $isPreview = !empty($this->response['iwg_preview']);

            $this->parser = new Parser($this->getTitle(), $this->response['extract'], $imagesResponseData, $isPreview);
        }

        return $this->parser;
    }

    public function __toString()
    {
        return $this->getBody();
    }
}
