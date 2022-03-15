<?php

namespace Illuminated\Wikipedia;

use InvalidArgumentException;

class Wikipedia extends Grabber
{
    /**
     * The language.
     */
    protected string $lang;

    /**
     * Create a new instance of Wikipedia grabber.
     */
    public function __construct(string $lang = 'en')
    {
        throw_unless(
            in_array($lang, ['en', 'ru']),
            new InvalidArgumentException("The given language (`{$lang}`) is not supported.")
        );

        $this->lang = $lang;

        parent::__construct();
    }

    /**
     * Get the base URI.
     */
    protected function baseUri(): string
    {
        return "https://{$this->lang}.wikipedia.org/w/api.php";
    }
}
