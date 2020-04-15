<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Arr;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Parser\Parser;

abstract class EntitySingular extends Entity
{
    /**
     * The parser.
     *
     * @var \Illuminated\Wikipedia\Grabber\Parser\Parser
     */
    protected $parser;

    /**
     * The response.
     *
     * @var array
     */
    protected $response;

    /**
     * Indicates whether grabbing was successful or not.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return !$this->isMissing()
            && !$this->isInvalid();
    }

    /**
     * Indicates whether the given page is missing or not.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     *
     * @return bool
     */
    public function isMissing()
    {
        return !empty($this->response['missing']);
    }

    /**
     * Indicates whether the given page is invalid or not.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     *
     * @return bool
     */
    public function isInvalid()
    {
        return !empty($this->response['invalid']);
    }

    /**
     * Indicates whether there's a disambiguation or not.
     *
     * @return bool
     */
    public function isDisambiguation()
    {
        return !empty($this->response['pageprops'])
            && isset($this->response['pageprops']['disambiguation']);
    }

    /**
     * Get the page id.
     *
     * @return int|null
     */
    public function getId()
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['pageid'];
    }

    /**
     * Get the title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['title'];
    }

    /**
     * Get the content in plain format.
     *
     * @return string
     */
    public function plain()
    {
        $this->format = 'plain';

        return $this->getBody();
    }

    /**
     * Get the content in Bulma format.
     *
     * @return string
     */
    public function bulma()
    {
        $this->format = 'bulma';

        return $this->getBody();
    }

    /**
     * Get the content in Bootstrap format.
     *
     * @return string
     */
    public function bootstrap()
    {
        $this->format = 'bootstrap';

        return $this->getBody();
    }

    /**
     * Get the body.
     *
     * @return string
     */
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

    /**
     * Get the body for missing pages.
     *
     * @return string
     */
    protected function getMissingBody()
    {
        return "The page `{$this->target}` does not exist.";
    }

    /**
     * Get the body for invalid pages.
     *
     * @return string
     */
    protected function getInvalidBody()
    {
        $reason = Arr::get($this->response, 'invalidreason');

        return "The page `{$this->target}` is invalid. {$reason}";
    }

    /**
     * Append the section.
     *
     * @param string $title
     * @param string $body
     * @param int $level
     * @return $this
     */
    public function append(string $title, string $body, int $level = 2)
    {
        $this->getSections()->push(new Section($title, $body, $level));

        return $this;
    }

    /**
     * Get the sections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSections()
    {
        return $this->getParser()->getSections();
    }

    /**
     * Get the parser.
     *
     * @return \Illuminated\Wikipedia\Grabber\Parser\Parser
     */
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

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getBody();
    }
}
