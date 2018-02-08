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
            'base_uri' => $this->baseUri(),
            'headers' => [
                'User-Agent' => $this->userAgent(),
            ],
        ]);
    }

    abstract protected function baseUri();

    protected function userAgent()
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
