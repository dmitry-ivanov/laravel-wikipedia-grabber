<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Target
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function targetParams($target)
    {
        if (is_int($target)) {
            return ['pageids' => $target];
        }

        return ['titles' => $target];
    }
}
