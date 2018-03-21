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
        $this->title = $title;
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
        $this->level = (int) $level;
    }
}
