<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class SectionsParser
{
    /**
     * The title.
     */
    protected string $title;

    /**
     * The body.
     */
    protected string $body;

    /**
     * The sections.
     */
    protected Collection $sections;

    /**
     * Create a new instance of the sections parser.
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Get the sections.
     */
    public function sections(): Collection
    {
        $this->sections = collect([$this->mainSection()]);

        foreach ($this->splitByTitles() as $item) {
            $this->handleItem($item);
        }

        return $this->sections;
    }

    /**
     * Get the main section.
     */
    protected function mainSection(): Section
    {
        return $this->section($this->title, 1);
    }

    /**
     * Get the section.
     */
    protected function section(string $title, int $level): Section
    {
        return new Section($title, '', $level);
    }

    /**
     * Split by titles.
     */
    protected function splitByTitles(): array
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}{$whitespace}.*?{$whitespace}{$marker})/";

        return preg_split($pattern, $this->body, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * Handle the item.
     */
    protected function handleItem(string $item): void
    {
        if ($this->isTitle($item)) {
            $section = $this->section($this->title($item), $this->level($item));
            $this->sections->push($section);
            return;
        }

        $last = $this->sections->pop();
        $last->setBody($item);
        $this->sections->push($last);
    }

    /**
     * Check whether the given item is title or not.
     */
    protected function isTitle(string $item): bool
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}.*?{$whitespace}{$marker}/";

        return (bool) preg_match($pattern, $item);
    }

    /**
     * Get title from the given title item.
     */
    protected function title(string $titleItem): string
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}(.*?){$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return $matches[1];
    }

    /**
     * Get level from the given title item.
     */
    protected function level(string $titleItem): int
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}){$whitespace}.*?{$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return strlen($matches[1]);
    }
}
