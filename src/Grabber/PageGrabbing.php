<?php

namespace Illuminated\Wikipedia\Grabber;

trait PageGrabbing
{
    public function page($title)
    {
        $params = $this->composePageParams($title);

        dd($params);
    }

    protected function composePageParams($title)
    {
        return [
            'query' => array_merge([
                'action' => 'query',
                'format' => 'json',
                'formatversion' => 2,
                'redirects' => true,
                'prop' => 'extracts',
            ], $this->composeTargetParams($title)),
        ];
    }
}
