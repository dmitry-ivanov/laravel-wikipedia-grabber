<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class WikitextImage extends Wikitext
{
    protected $name;
    protected $location;

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

        dump('--------------------------------------------------------'); //////////////////////////////////////////////
        dump($body); ///////////////////////////////////////////////////////////////////////////////////////////////////

        $body = $this->strip($body);
        $body = $this->plain($body);
        $parts = $this->explode($body);

        foreach ($parts as $part) {
            if ($this->isUnhandledPart($part)) {
                continue;
            }

            if ($this->isLocation($part)) {
                $this->location = $part;
                continue;
            }

            dump($part); ///////////////////////////////////////////////////////////////////////////////////////////////
        }
    }

    protected function strip($body)
    {
        if (starts_with($body, '[[')) {
            $body = str_replace_first('[[', '', $body);
        }

        if (ends_with($body, ']]')) {
            $body = str_replace_last(']]', '', $body);
        }

        return $body;
    }

    protected function explode($body)
    {
        $parts = explode('|', $body);
        $this->name = array_shift($parts);

        return $parts;
    }

    protected function isUnhandledPart($string)
    {
        return $this->isType($string)
            || $this->isBorder($string)
            || $this->isAlignment($string)
            || $this->isSize($string);
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

    protected function isAlignment($string)
    {
        return in_array($string, ['baseline', 'middle', 'sub', 'super', 'text-top', 'text-bottom', 'top', 'bottom']);
    }

    protected function isSize($string)
    {
        return in_array($string, ['upright'])
            || starts_with($string, ['upright='])
            || preg_match('/(\d+)px/', $string)
            || preg_match('/x(\d+)px/', $string)
            || preg_match('/(\d+)x(\d+)px/', $string);
    }

    protected function isLocation($string)
    {
        return in_array($string, ['right', 'left', 'center', 'none']);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
