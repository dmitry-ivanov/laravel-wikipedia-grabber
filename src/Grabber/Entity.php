<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Entity
{
    /**
     * The client.
     */
    protected Client $client;

    /**
     * The target, which might be either page id or page title.
     */
    protected int|string $target;

    /**
     * The format.
     */
    protected string $format;

    /**
     * Indicates whether we want to grab images or not.
     */
    protected bool $withImages;

    /**
     * An image size, in pixels.
     */
    protected int $imageSize;

    /**
     * An image size on preview, in pixels.
     */
    protected int $imageSizeOnPreview;

    /**
     * Create a new instance of the Entity.
     */
    public function __construct(Client $client, int|string $target)
    {
        $this->client = $client;
        $this->target = $target;
        $this->format = config('wikipedia-grabber.format');
        $this->withImages = (bool) config('wikipedia-grabber.images');
        $this->imageSize = (int) config('wikipedia-grabber.image_size');
        $this->imageSizeOnPreview = (int) config('wikipedia-grabber.image_size_on_preview');

        $this->grab();
    }

    /**
     * Grab the content.
     */
    abstract protected function grab(): void;

    /**
     * Get the MediaWiki API parameters.
     */
    abstract protected function params(): array;

    /**
     * Compose the target params.
     */
    protected function targetParams(): array
    {
        if (is_int($this->target)) {
            return ['pageids' => $this->target];
        }

        return ['titles' => $this->target];
    }

    /**
     * Make request with the given parameters.
     */
    protected function request(array $params): array
    {
        return json_decode($this->client->get('', $params)->getBody(), true);
    }
}
