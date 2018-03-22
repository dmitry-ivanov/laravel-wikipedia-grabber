<?php

namespace Illuminated\Wikipedia\Grabber\Component;

class Section
{
    protected $title;
    protected $body;
    protected $level;

    public function __construct($title, $body, $level)
    {
        $this->setTitle($title);
        $this->setBody($body);
        $this->setLevel($level);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = trim($title);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = trim($body);
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $level = (int) $level;

        if ($level < 1) {
            $level = 1;
        }

        $this->level = $level;
    }

    public function isMain()
    {
        return ($this->level == 1);
    }

    public function getHtmlLevel()
    {
        // We have only h1..h6 html tags.
        if ($this->level > 6) {
            return 6;
        }

        return $this->level;
    }
}
