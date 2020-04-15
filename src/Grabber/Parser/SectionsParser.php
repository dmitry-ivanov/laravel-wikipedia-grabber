<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminated\Wikipedia\Grabber\Component\Section;

class SectionsParser
{
    /**
     * The title.
     *
     * @var string
     */
    protected $title;

    /**
     * The body.
     *
     * @var string
     */
    protected $body;

    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * Create a new instance of the sections parser.
     *
     * @param string $title
     * @param string $body
     * @return void
     */
    public function __construct(string $title, string $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Get the sections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function sections()
    {
        $this->sections = collect([$this->mainSection()]);

        foreach ($this->splitByTitles() as $item) {
            $this->handleItem($item);
        }

        return $this->sections;
    }

    /**
     * Get the main section.
     *
     * @return \Illuminated\Wikipedia\Grabber\Component\Section
     */
    protected function mainSection()
    {
        return $this->section($this->title, 1);
    }

    /**
     * Get the section.
     *
     * @param string $title
     * @param int $level
     * @return \Illuminated\Wikipedia\Grabber\Component\Section
     */
    protected function section(string $title, int $level)
    {
        return new Section($title, '', $level);
    }

    /**
     * Split by titles.
     *
     * @return array|string[]
     */
    protected function splitByTitles()
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}{$whitespace}.*?{$whitespace}{$marker})/";

        return preg_split($pattern, $this->body, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    /**
     * Handle the item.
     *
     * @param string $item
     * @return void
     */
    protected function handleItem(string $item)
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
     *
     * @param string $item
     * @return bool
     */
    protected function isTitle(string $item)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}.*?{$whitespace}{$marker}/";

        return (bool) preg_match($pattern, $item);
    }

    /**
     * Get title from the given title item.
     *
     * @param string $titleItem
     * @return string
     */
    protected function title(string $titleItem)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/{$marker}{$whitespace}(.*?){$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return $matches[1];
    }

    /**
     * Get level from the given title item.
     *
     * @param string $titleItem
     * @return int
     */
    protected function level(string $titleItem)
    {
        $marker = '[=]{2,}';
        $whitespace = '\s*';
        $pattern = "/({$marker}){$whitespace}.*?{$whitespace}{$marker}/";

        preg_match($pattern, $titleItem, $matches);

        return strlen($matches[1]);
    }
}
