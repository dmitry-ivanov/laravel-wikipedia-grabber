<?php

namespace Illuminated\Wikipedia;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Grabber\Page;
use Illuminated\Wikipedia\Grabber\Preview;
use Illuminated\Wikipedia\Grabber\Random;

abstract class Grabber
{
    /**
     * The client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Create a new instance of the Grabber.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUri(),
            'headers' => [
                'User-Agent' => $this->userAgent(),
            ],
        ]);
    }

    /**
     * Get the client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Grab page by the given title or id.
     *
     * @param string|int $title
     * @return \Illuminated\Wikipedia\Grabber\Page
     */
    public function page($title)
    {
        return new Page($this->client, $title);
    }

    /**
     * Grab preview by the given title or id.
     *
     * @param string|int $title
     * @return \Illuminated\Wikipedia\Grabber\Preview
     */
    public function preview($title)
    {
        return new Preview($this->client, $title);
    }

    /**
     * Grab random page.
     *
     * @return \Illuminated\Wikipedia\Grabber\Page
     */
    public function randomPage()
    {
        return $this->page($this->randomTitle());
    }

    /**
     * Grab random preview.
     *
     * @return \Illuminated\Wikipedia\Grabber\Preview
     */
    public function randomPreview()
    {
        return $this->preview($this->randomTitle());
    }

    /**
     * Get random title.
     *
     * @return string
     */
    protected function randomTitle()
    {
        return (new Random($this->client))->title();
    }

    /**
     * Get the base URI.
     *
     * @return string
     */
    abstract protected function baseUri();

    /**
     * Compose the User Agent.
     *
     * @return string
     */
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
