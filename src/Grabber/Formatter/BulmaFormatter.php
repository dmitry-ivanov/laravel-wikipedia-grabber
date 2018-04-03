<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Section;

class BulmaFormatter extends Formatter
{
    public function tableOfContents(Collection $sections)
    {
        dd('bulma toc');
    }

    public function section(Section $section)
    {
        dd('bulma section');
    }
}
