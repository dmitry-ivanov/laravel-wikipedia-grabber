<?php

namespace Illuminated\Wikipedia\Grabber;

trait PageGrabbing
{
    public function page($title)
    {
        $params = $this->composeTargetParams($title);

        dd($params);
    }
}
