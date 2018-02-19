<?php

namespace Illuminated\Wikipedia;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Grabber\Page;

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

    public function page($title)
    {
        $page = new Page($this->client, $title);

        dd($title, $page);
        // $response = $this->client->get('', $this->pageParams($title));
        // $body = json_decode($response->getBody(), true);
        //
        // return new Page($body);
    }

    public function preview($title)
    {
        dd($title);
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
