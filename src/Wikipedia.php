<?php

namespace Illuminated\Wikipedia;

class Wikipedia extends Grabber
{
    protected $lang;

    public function __construct($lang = 'en')
    {
        $this->lang = $lang;

        parent::__construct();
    }

    protected function baseUri()
    {
        return "https://{$this->lang}.wikipedia.org/w/api.php";
    }
}
