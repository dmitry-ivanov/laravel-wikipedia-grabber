<?php

namespace Illuminated\Wikipedia;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\Grabber\Random;
use Illuminated\Wikipedia\Grabber\Preview;

abstract class Grabber
{
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

    public function getClient()
    {
        return $this->client;
    }

    public function page($title)
    {
        return new Page($this->client, $title);
    }

    public function preview($title)
    {
        return new Preview($this->client, $title);
    }

    public function randomPage()
    {
        return $this->page($this->randomTitle());
    }

    public function randomPreview()
    {
        return $this->preview($this->randomTitle());
    }

    protected function randomTitle()
    {
        return (new Random($this->client))->title();
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
}
