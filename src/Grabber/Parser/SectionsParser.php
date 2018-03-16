<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

class SectionsParser
{
    protected $title;
    protected $body;
    protected $sections;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function sections()
    {
        $this->sections = collect([$this->mainSection()]);

        foreach ($this->splitByTitles() as $item) {
            $this->handleItem($item);
        }

        return $this->sections;
    }

    private function mainSection()
    {
        return $this->section($this->title, 1);
    }

    private function section($title, $level)
    {
        return [
            'title' => $title,
            'level' => $level,
            'body' => null,
        ];
    }

    private function splitByTitles()
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}{$whitespace}.*?{$whitespace}{$marker})/";

        return preg_split($pattern, $this->body, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    private function handleItem($item)
    {
        if ($this->isTitle($item)) {
            return $this->sections->push(
                $this->section($this->title($item), $this->level($item))
            );
        }

        $last = $this->sections->pop();
        $last['body'] = trim($item);
        $this->sections->push($last);
    }

    private function isTitle($item)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}.*?{$whitespace}{$marker}/";

        return preg_match($pattern, $item);
    }

    private function level($titleItem)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}){$whitespace}.*?{$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return strlen($matches[1]);
    }

    private function title($titleItem)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}(.*?){$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return $matches[1];
    }
}
