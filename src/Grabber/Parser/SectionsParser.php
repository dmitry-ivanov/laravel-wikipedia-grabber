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
        $sections = [[
            'level' => 1,
            'title' => $this->title,
            'body' => null,
        ]];

        foreach ($this->parse() as $item) {
            dd($item);
        }

        dd($parts);
    }

    private function parse()
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}{$whitespace}.*?{$whitespace}{$marker})/";

        return preg_split($pattern, $this->body, -1, PREG_SPLIT_DELIM_CAPTURE);
    }
}
