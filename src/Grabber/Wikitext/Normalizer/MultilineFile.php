<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineFile
{
    /**
     * Flatten the multiline file elements.
     */
    public function flatten(string $wikitext): string
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

    /**
     * Determine whether the given line opens a multiline file element or not.
     */
    protected function isFileOpened(string $line): bool
    {
        if (!Str::contains($line, '[[File:')) {
            return false;
        }

        $line = str_replace('[[File:', '/!! IWG-MULTILINE-FILE !!/', $line);

        return !$this->isFileClosed($line);
    }

    /**
     * Determine whether the given line closes a multiline file element or not.
     */
    protected function isFileClosed(string $line): bool
    {
        $line = (new Wikitext($line))->removeLinks();

        return Str::contains($line, ']]');
    }
}
