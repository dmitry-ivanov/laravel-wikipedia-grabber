<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineFile
{
    public function flatten($wikitext)
    {
        $flatten = collect();

        $isFileOpened = false;
        $lines = preg_split("/\r\n|\n|\r/", $wikitext);
        foreach ($lines as $line) {
            if ($isFileOpened) {
                $flatten->push($flatten->pop() . ' ' . $line);
                $isFileOpened = !$this->isFileClosed($line);
            } else {
                $flatten->push($line);
                $isFileOpened = $this->isFileOpened($line);
            }
        }

        return $flatten->implode("\n");
    }

    protected function isFileOpened($line)
    {
        if (!str_contains($line, '[[File:')) {
            return false;
        }

        $line = str_replace('[[File:', '/!! IWG-MULTILINE-FILE !!/', $line);

        return !$this->isFileClosed($line);
    }

    protected function isFileClosed($line)
    {
        $line = (new Wikitext($line))->removeLinks();
        return str_contains($line, ']]');
    }
}
