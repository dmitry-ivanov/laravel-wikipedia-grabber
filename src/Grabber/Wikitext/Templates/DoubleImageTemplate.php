<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

/**
 * @see https://en.wikipedia.org/wiki/Template:Double_image
 * @see https://ru.wikipedia.org/wiki/Шаблон:Сдвоенное_изображение
 */
class DoubleImageTemplate
{
    protected $body;
    protected $left;
    protected $right;

    public function __construct($body)
    {
        $this->body = $body;
    }

    public function extract($file)
    {
        $body = $this->body;
        $body = str_replace_first('{{', '', $body);
        $body = str_replace_last('}}', '', $body);

        $parts = explode('|', $body);
        $position = array_get($parts, 1);
        $this->left = array_get($parts, 2);
        $this->right = array_get($parts, 4);
        $leftCaption = array_get($parts, 6);
        $rightCaption = array_get($parts, 7);

        if (empty($leftCaption) && !empty($rightCaption)) {
            $leftCaption = $rightCaption;
        }

        if (empty($rightCaption) && !empty($leftCaption)) {
            $rightCaption = $leftCaption;
        }

        if ($this->isLeft($file)) {
            return "{$file}|{$position}|{$leftCaption}";
        }

        if ($this->isRight($file)) {
            return "{$file}|{$position}|{$rightCaption}";
        }

        return $this->body;
    }

    protected function isLeft($file)
    {
        $fileWithSpaces = str_replace('_', ' ', $file);
        $fileWithUnderscores = str_replace(' ', '_', $file);

        return ($file == $this->left)
            || ($fileWithSpaces == $this->left)
            || ($fileWithUnderscores == $this->left);
    }

    protected function isRight($file)
    {
        $fileWithSpaces = str_replace('_', ' ', $file);
        $fileWithUnderscores = str_replace(' ', '_', $file);

        return ($file == $this->right)
            || ($fileWithSpaces == $this->right)
            || ($fileWithUnderscores == $this->right);
    }
}
