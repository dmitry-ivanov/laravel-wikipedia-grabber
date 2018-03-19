<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Entity
{
    protected $client;
    protected $target;
    protected $format;

    public function __construct(Client $client, $target)
    {
        $this->client = $client;
        $this->target = $target;
        $this->format = config('wikipedia-grabber.format');

        $this->grab();
    }

    abstract protected function grab();

    abstract protected function params();

    protected function targetParams()
    {
        if (is_int($this->target)) {
            return ['pageids' => $this->target];
        }

        return ['titles' => $this->target];
    }
}
