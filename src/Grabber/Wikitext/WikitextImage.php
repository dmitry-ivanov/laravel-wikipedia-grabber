<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class WikitextImage extends Wikitext
{
    protected $position;

    public function __construct($body)
    {
        parent::__construct($body);

        $this->parse();
    }

    /**
     * @see https://en.wikipedia.org/wiki/Wikipedia:Extended_image_syntax
     */
    protected function parse()
    {
        $body = $this->body;
        $body = str_replace_first('[[', '', $body);
        $body = str_replace_last(']]', '', $body);

        $parts = explode('|', $body);
        array_shift($parts);

        foreach ($parts as $part) {
            if ($this->isType($part) || $this->isBorder($part)) {
                continue;
            }

            dump($part);
        }
    }

    protected function isType($string)
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || starts_with($string, ['thumb=', 'thumbnail=']);
    }

    protected function isBorder($string)
    {
        return ($string == 'border');
    }

    public function getPosition()
    {
        return $this->position;
    }
}
