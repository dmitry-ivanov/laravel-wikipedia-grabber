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
     */
    protected Client $client;

    /**
     * Create a new instance of the Grabber.
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
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Grab page by the given title or id.
     */
    public function page(string|int $title): Page
    {
        return new Page($this->client, $title);
    }

    /**
     * Grab preview by the given title or id.
     */
    public function preview(string|int $title): Preview
    {
        return new Preview($this->client, $title);
    }

    /**
     * Grab random page.
     */
    public function randomPage(): Page
    {
        return $this->page($this->randomTitle());
    }

    /**
     * Grab random preview.
     */
    public function randomPreview(): Preview
    {
        return $this->preview($this->randomTitle());
    }

    /**
     * Get random title.
     */
    protected function randomTitle(): string
    {
        return (new Random($this->client))->title();
    }

    /**
     * Get the base URI.
     */
    abstract protected function baseUri(): string;

    /**
     * Compose the User Agent.
     */
    protected function userAgent(): string
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
