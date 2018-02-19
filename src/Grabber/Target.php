<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Target
{
    protected $client;
    protected $target;
    protected $response;

    public function __construct(Client $client, $target)
    {
        $this->client = $client;
        $this->target = $target;

        $this->grab();
    }

    protected function grab()
    {
        $this->response = json_decode(
            $this->client->get('', $this->params())->getBody(),
            true
        );
    }

    abstract protected function params();

    protected function targetParams()
    {
        if (is_int($this->target)) {
            return ['pageids' => $this->target];
        }

        return ['titles' => $this->target];
    }
}
