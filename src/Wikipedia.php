<?php

namespace Illuminated\Wikipedia;

class Wikipedia extends Grabber
{
    private $lang;

    public function __construct($lang = 'en')
    {
        $this->lang = $lang;

        parent::__construct();
    }
}
