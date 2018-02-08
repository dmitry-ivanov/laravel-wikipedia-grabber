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
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'User-Agent' => $this->getUserAgent(),
            ],
        ]);
    }

    abstract protected function getBaseUri();

    protected function getUserAgent()
    {
        $userAgent = config('wikipedia-grabber.user_agent');
        if (!empty($userAgent)) {
            return $userAgent;
        }

        $name = config('app.name');
        $url = config('app.url');

        return "{$name} ({$url})";
    }

    protected function targetParams($target)
    {
        if (is_int($target)) {
            return ['pageids' => $target];
        }

        return ['titles' => $target];
    }
}
