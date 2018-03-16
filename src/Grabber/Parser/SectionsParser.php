<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

class SectionsParser
{
    protected $title;
    protected $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function sections()
    {
        $sections = collect([$this->section($this->title, 1)]);

        $items = $this->parse();
        foreach ($items as $item) {
            if ($this->isTitle($item)) {
                $title = $this->title($item);
                $level = $this->level($item);
                $sections->push($this->section($title, $level));
            } else {
                $last = $sections->pop();
                $last['body'] = trim($item);
                $sections->push($last);
            }
        }

        return $sections;
    }

    private function parse()
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}{$whitespace}.*?{$whitespace}{$marker})/";

        return preg_split($pattern, $this->body, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    private function section($title, $level)
    {
        return [
            'level' => $level,
            'title' => $title,
            'body' => null,
        ];
    }

    private function isTitle($subject)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}.*?{$whitespace}{$marker}/";

        return preg_match($pattern, $subject);
    }

    private function level($subject)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}){$whitespace}.*?{$whitespace}{$marker}/";

        preg_match($pattern, $subject, $matches);

        return strlen($matches[1]);
    }

    private function title($subject)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}(.*?){$whitespace}{$marker}/";

        preg_match($pattern, $subject, $matches);

        return $matches[1];
    }
}
