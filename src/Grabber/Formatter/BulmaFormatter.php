<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminated\Wikipedia\Grabber\Partial\Section;

class BulmaFormatter extends Formatter
{
    public function section(Section $section)
    {
        dd('bulma section');
    }
}
