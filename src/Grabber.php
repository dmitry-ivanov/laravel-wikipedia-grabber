<?php

namespace Illuminated\Wikipedia;

use Illuminated\Wikipedia\Grabber\PageGrabbing;
use Illuminated\Wikipedia\Grabber\PreviewGrabbing;

abstract class Grabber
{
    use PageGrabbing;
    use PreviewGrabbing;
}
