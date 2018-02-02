<?php

namespace Illuminated\Wikipedia;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Grabber\PageGrabbing;
use Illuminated\Wikipedia\Grabber\PreviewGrabbing;

abstract class Grabber
{
    use PageGrabbing;
    use PreviewGrabbing;

    protected $client;

    public function __construct()
    {
        $name = config('app.name');
        $url = config('app.url');

        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'User-Agent' => "{$name} ({$url})",
            ],
        ]);
    }

    abstract protected function getBaseUri();
}
