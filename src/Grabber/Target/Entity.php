<?php

namespace Illuminated\Wikipedia\Grabber\Target;

use GuzzleHttp\Client;

abstract class Entity
{
    protected $client;
    protected $target;

    public function __construct(Client $client, $target)
    {
        $this->client = $client;
        $this->target = $target;

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