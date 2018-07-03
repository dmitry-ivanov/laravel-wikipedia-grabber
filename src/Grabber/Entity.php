<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Entity
{
    protected $client;
    protected $target;
    protected $format;
    protected $withImages;
    protected $imageSize;

    public function __construct(Client $client, $target)
    {
        $this->client = $client;
        $this->target = $target;
        $this->format = config('wikipedia-grabber.format');
        $this->withImages = (bool) config('wikipedia-grabber.images');
        $this->imageSize = (int) config('wikipedia-grabber.image_size');

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

    protected function request(array $params)
    {
        return json_decode(
            $this->client->get('', $params)->getBody(),
            true
        );
    }
}
