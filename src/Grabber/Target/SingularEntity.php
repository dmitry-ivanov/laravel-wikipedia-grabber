<?php

namespace Illuminated\Wikipedia\Grabber\Target;

use Illuminated\Wikipedia\Grabber\Parser\Parser;

abstract class SingularEntity extends Entity
{
    use VariousFormatters;

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

    public function getBody()
    {
        if ($this->isMissing()) {
            return $this->getMissingBody();
        }

        if ($this->isInvalid()) {
            return $this->getInvalidBody();
        }

        $parser = new Parser($this->getTitle(), $this->response['extract']);

        return $parser->parse($this->format);
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
