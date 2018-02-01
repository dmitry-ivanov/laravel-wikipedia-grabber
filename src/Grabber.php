<?php

namespace Illuminated\Wikipedia;

abstract class Grabber
{
    public function page()
    {
        return 'page';
    }

    public function preview()
    {
        return 'preview';
    }
}
