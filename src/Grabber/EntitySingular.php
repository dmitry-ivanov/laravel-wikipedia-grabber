<?php

namespace Illuminated\Wikipedia\Grabber;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Parser\Parser;

abstract class EntitySingular extends Entity
{
    /**
     * The parser.
     */
    protected Parser $parser;

    /**
     * The response.
     */
    protected array $response;

    /**
     * Indicates whether grabbing was successful or not.
     */
    public function isSuccess(): bool
    {
        return !$this->isMissing()
            && !$this->isInvalid();
    }

    /**
     * Indicates whether the given page is missing or not.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     */
    public function isMissing(): bool
    {
        return !empty($this->response['missing']);
    }

    /**
     * Indicates whether the given page is invalid or not.
     *
     * @see https://www.mediawiki.org/wiki/API:Query#Missing_and_invalid_titles
     */
    public function isInvalid(): bool
    {
        return !empty($this->response['invalid']);
    }

    /**
     * Indicates whether there's a disambiguation or not.
     */
    public function isDisambiguation(): bool
    {
        return !empty($this->response['pageprops'])
            && isset($this->response['pageprops']['disambiguation']);
    }

    /**
     * Get the page id.
     */
    public function getId(): int|null
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['pageid'];
    }

    /**
     * Get the title.
     */
    public function getTitle(): string|null
    {
        if (!$this->isSuccess()) {
            return null;
        }

        return $this->response['title'];
    }

    /**
     * Get the content in plain format.
     */
    public function plain(): string
    {
        $this->format = 'plain';

        return $this->getBody();
    }

    /**
     * Get the content in Bulma format.
     */
    public function bulma(): string
    {
        $this->format = 'bulma';

        return $this->getBody();
    }

    /**
     * Get the content in Bootstrap format.
     */
    public function bootstrap(): string
    {
        $this->format = 'bootstrap';

        return $this->getBody();
    }

    /**
     * Get the body.
     */
    public function getBody(): string
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
     */
    protected function getMissingBody(): string
    {
        return "The page `{$this->target}` does not exist.";
    }

    /**
     * Get the body for invalid pages.
     */
    protected function getInvalidBody(): string
    {
        $reason = Arr::get($this->response, 'invalidreason');

        return "The page `{$this->target}` is invalid. {$reason}";
    }

    /**
     * Append the section.
     */
    public function append(string $title, string $body, int $level = 2): self
    {
        $this->getSections()->push(new Section($title, $body, $level));

        return $this;
    }

    /**
     * Get the sections.
     */
    public function getSections(): Collection
    {
        return $this->getParser()->getSections();
    }

    /**
     * Get the parser.
     */
    protected function getParser(): Parser
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
     */
    public function __toString(): string
    {
        return $this->getBody();
    }
}
