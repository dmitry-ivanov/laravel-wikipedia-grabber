<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Image
{
    /**
     * The URL.
     */
    protected string $url;

    /**
     * The width.
     */
    protected int $width;

    /**
     * The height.
     */
    protected int $height;

    /**
     * The URL to original.
     */
    protected string $originalUrl;

    /**
     * The position.
     */
    protected string $position;

    /**
     * The description.
     */
    protected string $description;

    /**
     * The MIME type.
     */
    protected ?string $mime;

    /**
     * Create a new instance of the Image.
     */
    public function __construct(string $url, int $width, int $height, string $originalUrl, string $position = 'right', string $description = '', string $mime = null)
    {
        $this->setUrl($url);
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setOriginalUrl($originalUrl);
        $this->setPosition($position);
        $this->setDescription($description);
        $this->setMime($mime);
    }

    /**
     * Get the URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the URL.
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Get the width.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Set the width.
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    /**
     * Get the height.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Set the height.
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * Get the URL to original.
     */
    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    /**
     * Set the URL to original.
     */
    public function setOriginalUrl(string $originalUrl): void
    {
        $this->originalUrl = $originalUrl;
    }

    /**
     * Get the position.
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * Set the position.
     */
    public function setPosition(string $position): void
    {
        if (!in_array($position, ['left', 'right'])) {
            $position = 'right';
        }

        $this->position = $position;
    }

    /**
     * Get the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description.
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the MIME type.
     */
    public function getMime(): string|null
    {
        return $this->mime;
    }

    /**
     * Set the MIME type.
     */
    public function setMime(string $mime = null): void
    {
        $this->mime = mb_strtolower($mime, 'utf-8');
    }

    /**
     * Get the alternative text.
     */
    public function getAlt(): string
    {
        return htmlspecialchars($this->getDescription(), ENT_QUOTES);
    }

    /**
     * Check whether an object is audio or not.
     */
    public function isAudio(): bool
    {
        $originalUrl = mb_strtolower($this->getOriginalUrl(), 'utf-8');

        if (Str::endsWith($originalUrl, ['oga', 'mp3', 'wav'])) {
            return true;
        }

        if (Str::endsWith($originalUrl, 'ogg')) {
            return !Str::contains($this->getMime(), 'video');
        }

        return false;
    }

    /**
     * Check whether an object is video or not.
     */
    public function isVideo(): bool
    {
        $originalUrl = mb_strtolower($this->getOriginalUrl(), 'utf-8');

        if (Str::endsWith($originalUrl, ['ogv', 'mp4', 'webm'])) {
            return true;
        }

        if (Str::endsWith($originalUrl, 'ogg')) {
            return Str::contains($this->getMime(), 'video');
        }

        return false;
    }

    /**
     * Get the transcoded mp3 URL.
     */
    public function getTranscodedMp3Url(): string|false
    {
        $originalUrl = $this->getOriginalUrl();
        $originalUrlLowercased = mb_strtolower($originalUrl, 'utf-8');

        if (!$this->isAudio() || Str::endsWith($originalUrlLowercased, 'mp3')) {
            return false;
        }

        $start = preg_quote('://upload.wikimedia.org/wikipedia', '/');
        if (!preg_match("/(.*?{$start}\/.*?)\/(.*)/i", $originalUrl, $matches)) {
            return false;
        }

        $name = basename($originalUrl);

        return "{$matches[1]}/transcoded/{$matches[2]}/{$name}.mp3";
    }

    /**
     * Get the transcoded webm URLs.
     */
    public function getTranscodedWebmUrls(): Collection|false
    {
        $originalUrl = $this->getOriginalUrl();

        if (!$this->isVideo()) {
            return false;
        }

        $start = preg_quote('://upload.wikimedia.org/wikipedia', '/');
        if (!preg_match("/(.*?{$start}\/.*?)\/(.*)/i", $originalUrl, $matches)) {
            return false;
        }

        $name = basename($originalUrl);

        return collect(['720p', '480p', '360p', '240p', '160p'])->map(function ($quality) use ($matches, $name) {
            return "{$matches[1]}/transcoded/{$matches[2]}/{$name}.{$quality}.webm";
        });
    }
}
