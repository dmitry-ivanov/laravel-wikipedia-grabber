<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

use Illuminated\Wikipedia\Grabber\Component\Image;

class Wikitext
{
    protected $body;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function createImageObject(array $image)
    {
        $imageInfo = head($image['imageinfo']);

        $url = $imageInfo['thumburl'];
        $width = $imageInfo['thumbwidth'];
        $height = $imageInfo['thumbheight'];
        $originalUrl = $imageInfo['url'];

        $imageWikitext = $this->getImageWikitext($image);
        $position = $this->getImagePosition($imageWikitext);

        return new Image($url, $width, $height, $originalUrl, $position, $image['title']);
    }

    protected function getImageWikitext(array $image)
    {
        $file = last(explode(':', $image['title']));

        return collect(preg_split('/\R/', $this->body))->first(function ($line) use ($file) {
            return str_contains($line, $file);
        });
    }

    protected function getImagePosition($imageWikitext)
    {
        $parts = explode('|', trim($imageWikitext, '[]'));
        return in_array('left', $parts) ? 'left' : 'right';
    }

    public function sanitize()
    {
        return $this->removeLinks();
    }

    protected function removeLinks()
    {
        $sanitized = $this->body;

        if (!preg_match_all('/\[\[(.*?)\]\]/', $this->body, $matches, PREG_SET_ORDER)) {
            return $sanitized;
        }

        foreach ($matches as $match) {
            $link = $match[0];
            $title = last(explode('|', $match[1]));
            $sanitized = str_replace_first($link, $title, $sanitized);
        }

        return $sanitized;
    }
}
