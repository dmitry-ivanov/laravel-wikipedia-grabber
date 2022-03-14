<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ConvertTemplate;

/**
 * @see https://en.wikipedia.org/wiki/Help:Link
 * @see https://en.wikipedia.org/wiki/Help:Template
 * @see https://en.wikipedia.org/wiki/Template:Nowrap
 * @see https://www.mediawiki.org/wiki/Help:Formatting
 */
class Wikitext
{
    /**
     * The body.
     */
    protected string $body;

    /**
     * Create a new instance of the Wikitext.
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * Get plain wikitext.
     */
    public function plain(string $body = null): string
    {
        $body = $body ?? $this->body;

        $body = $this->removeLinks($body);
        $body = $this->removeTemplates($body);
        $body = $this->removeHtmlTags($body);
        $body = $this->removeFormatting($body);

        return $body;
    }

    /**
     * Remove formatting.
     */
    public function removeFormatting(string $body = null): string
    {
        $body = $body ?? $this->body;

        return preg_replace("/'{2,}/", '', $body);
    }

    /**
     * Remove links.
     */
    public function removeLinks(string $body = null): string
    {
        $body = $body ?? $this->body;

        $placeholder = '/!! IWG-FILE-IN-FILE !!/';
        $body = str_replace('[[File:', $placeholder, $body);

        preg_match_all('/\[\[(.*?)\]\]/', $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $link = $match[0];
            $title = last(explode('|', $match[1]));
            $body = Str::replaceFirst($link, $title, $body);
        }

        $body = str_replace($placeholder, '[[File:', $body);
        preg_match_all('/\[\[File:.*?\]\]/', $body, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $file = $match[0];
            $isInProcessing = Str::contains($file, '/!! IWG-');
            if ($isInProcessing) {
                continue;
            }

            $body = Str::replaceFirst($file, '', $body);
        }

        return $body;
    }

    /**
     * Remove templates.
     */
    public function removeTemplates(string $body = null): string
    {
        $body = $body ?? $this->body;

        if (!preg_match_all('/\{\{(.*?)\}\}/', $body, $matches, PREG_SET_ORDER)) {
            return $body;
        }

        foreach ($matches as $match) {
            $template = $match[0];
            $templateBody = $match[1];
            $bodyInLowercase = mb_strtolower($templateBody, 'utf-8');

            $isIgnored = Str::startsWith($bodyInLowercase, [
                'sfn', 'cite',
                'section link', 'anchor', 'якорь',
                'see below', 'below', 'см. ниже', 'ниже',
                'see above', 'above', 'see at', 'см. выше', 'выше', 'переход',
            ]);
            $isConvert = Str::startsWith($bodyInLowercase, 'convert');
            $isSpace = Str::startsWith($bodyInLowercase, ['nbsp', 'space', 'clear', 'clr', '-']);

            if ($isIgnored) {
                $replace = '';
            } elseif ($isSpace) {
                $replace = ' ';
            } elseif ($isConvert) {
                $replace = (new ConvertTemplate($template))->extract();
            } else {
                $replace = last(explode('|', $templateBody));
                $replace = " {$replace}";
            }

            $body = Str::replaceFirst($template, $replace, $body);
        }

        $body = $this->removeMultipleSpaces($body);

        return $body;
    }

    /**
     * Remove HTML tags.
     */
    public function removeHtmlTags(string $body = null): string
    {
        $body = $body ?? $this->body;

        $body = preg_replace('/<br.*?>/', ' ', $body);
        $body = preg_replace('/<ref.*?\/(ref){0,1}>/', '', $body);

        $body = strip_tags($body);
        $body = $this->removeMultipleSpaces($body);

        return $body;
    }

    /**
     * Remove multiple spaces.
     */
    protected function removeMultipleSpaces(string $body): string
    {
        return preg_replace('/ {2,}/', ' ', $body);
    }
}
