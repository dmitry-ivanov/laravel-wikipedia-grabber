<?php

namespace Illuminated\Wikipedia\Grabber\Component;

use Illuminate\Support\Str;

class Image
{
    /**
     * The URL.
     *
     * @var string
     */
    protected $url;

    /**
     * The width.
     *
     * @var int
     */
    protected $width;

    /**
     * The height.
     *
     * @var int
     */
    protected $height;

    /**
     * The URL to original.
     *
     * @var string
     */
    protected $originalUrl;

    /**
     * The position.
     *
     * @var string
     */
    protected $position;

    /**
     * The description.
     *
     * @var string
     */
    protected $description;

    /**
     * The MIME type.
     *
     * @var string|null
     */
    protected $mime;

    /**
     * Create a new instance of the Image.
     *
     * @param string $url
     * @param int $width
     * @param int $height
     * @param string $originalUrl
     * @param string $position
     * @param string $description
     * @param string|null $mime
     * @return void
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
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL.
     *
     * @param string $url
     * @return void
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the width.
     *
     * @param int $width
     * @return void
     */
    public function setWidth(int $width)
    {
        $this->width = $width;
    }

    /**
     * Get the height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the height.
     *
     * @param int $height
     * @return void
     */
    public function setHeight(int $height)
    {
        $this->height = $height;
    }

    /**
     * Get the URL to original.
     *
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * Set the URL to original.
     *
     * @param string $originalUrl
     * @return void
     */
    public function setOriginalUrl(string $originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

    /**
     * Get the position.
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the position.
     *
     * @param string $position
     * @return void
     */
    public function setPosition(string $position)
    {
        if (!in_array($position, ['left', 'right'])) {
            $position = 'right';
        }

        $this->position = $position;
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description.
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get the MIME type.
     *
     * @return string|null
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set the MIME type.
     *
     * @param string|null $mime
     * @return void
     */
    public function setMime(string $mime = null)
    {
        $this->mime = mb_strtolower($mime, 'utf-8');
    }

    /**
     * Get the alternative text.
     *
     * @return string
     */
    public function getAlt()
    {
        return htmlspecialchars($this->getDescription(), ENT_QUOTES);
    }

    /**
     * Check whether an object is audio or not.
     *
     * @return bool
     */
    public function isAudio()
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
     *
     * @return bool
     */
    public function isVideo()
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
     *
     * @return string|false
     */
    public function getTranscodedMp3Url()
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
     *
     * @return \Illuminate\Support\Collection|false
     */
    public function getTranscodedWebmUrls()
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
