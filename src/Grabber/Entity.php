<?php

namespace Illuminated\Wikipedia\Grabber;

use GuzzleHttp\Client;

abstract class Entity
{
    /**
     * The client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The target, which might be either page id or page title.
     *
     * @var int|string
     */
    protected $target;

    /**
     * The format.
     *
     * @var string
     */
    protected $format;

    /**
     * Indicates whether we want to grab images or not.
     *
     * @var bool
     */
    protected $withImages;

    /**
     * An image size, in pixels.
     *
     * @var int
     */
    protected $imageSize;

    /**
     * An image size on preview, in pixels.
     *
     * @var int
     */
    protected $imageSizeOnPreview;

    /**
     * Create a new instance of the Entity.
     *
     * @param \GuzzleHttp\Client $client
     * @param string|int $target
     * @return void
     */
    public function __construct(Client $client, $target)
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
     *
     * @return void
     */
    abstract protected function grab();

    /**
     * Get the MediaWiki API parameters.
     *
     * @return array
     */
    abstract protected function params();

    /**
     * Compose the target params.
     *
     * @return array
     */
    protected function targetParams()
    {
        if (is_int($this->target)) {
            return ['pageids' => $this->target];
        }

        return ['titles' => $this->target];
    }

    /**
     * Make request with the given parameters.
     *
     * @param array $params
     * @return array
     */
    protected function request(array $params)
    {
        return json_decode($this->client->get('', $params)->getBody(), true);
    }
}
