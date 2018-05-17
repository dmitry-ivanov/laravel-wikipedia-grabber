<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

class WikitextImage extends Wikitext
{
    protected $name;
    protected $type;
    protected $border;
    protected $location;
    protected $alignment;
    protected $size;
    protected $link;
    protected $alt;
    protected $langtag;
    protected $caption;

    public function __construct($body)
    {
        parent::__construct($body);

        $this->parse();
    }

    /**
     * @see https://en.wikipedia.org/wiki/Wikipedia:Extended_image_syntax
     * @see https://ru.wikipedia.org/wiki/Википедия:Иллюстрирование
     */
    protected function parse()
    {
        $body = $this->body;

        $body = $this->strip($body);
        $body = $this->plain($body);
        $parts = $this->explode($body);

        foreach ($parts as $part) {
            if ($this->handle($part)) {
                continue;
            }

            $this->caption = $part;
        }
    }

    protected function strip($body)
    {
        if (starts_with($body, '[[')) {
            $body = str_replace_first('[[', '', $body);
        }

        if (ends_with($body, ']]')) {
            $body = str_replace_last(']]', '', $body);
        }

        return $body;
    }

    protected function explode($body)
    {
        $parts = explode('|', $body);
        $this->setName(array_shift($parts));

        return $parts;
    }

    protected function handle($part)
    {
        $fields = ['type', 'border', 'location', 'alignment', 'size', 'link', 'alt', 'langtag'];

        foreach ($fields as $field) {
            $is = camel_case("is_{$field}");
            $set = camel_case("set_{$field}");
            if ($this->{$is}($part)) {
                $this->{$set}($part);
                return true;
            }
        }

        if ($this->isSomeParameter($part)) {
            return true;
        }

        return false;
    }

    protected function isType($string)
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || in_array($string, ['мини', 'миниатюра'])
            || starts_with($string, ['thumb=', 'thumbnail=']);
    }

    protected function isBorder($string)
    {
        return ($string == 'border');
    }

    protected function isLocation($string)
    {
        return in_array($string, ['right', 'left', 'center', 'none'])
            || in_array($string, ['справа', 'слева', 'центр']);
    }

    protected function isAlignment($string)
    {
        return in_array($string, ['baseline', 'middle', 'sub', 'super', 'text-top', 'text-bottom', 'top', 'bottom']);
    }

    protected function isSize($string)
    {
        return in_array($string, ['upright'])
            || starts_with($string, ['upright='])
            || preg_match('/(\d+)px/', $string) || preg_match('/x(\d+)px/', $string) || preg_match('/(\d+)x(\d+)px/', $string)
            || preg_match('/(\d+)пкс/', $string) || preg_match('/x(\d+)пкс/', $string) || preg_match('/(\d+)x(\d+)пкс/', $string);
    }

    protected function isLink($string)
    {
        return starts_with($string, ['link=']);
    }

    protected function isAlt($string)
    {
        return starts_with($string, 'alt=')
            || starts_with($string, 'альт=');
    }

    protected function isLangtag($string)
    {
        return starts_with($string, ['lang=']);
    }

    protected function isSomeParameter($string)
    {
        return preg_match('/(.+?)=(.+?)/', $string);
    }

    public function getDescription()
    {
        if ($caption = $this->getCaption()) {
            return $caption;
        }

        if ($alt = $this->getAlt()) {
            return last(explode('=', $alt));
        }

        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        $name = str_replace_first('Файл:', 'File:', $name);

        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    protected function setType($type)
    {
        $this->type = $this->normalize($type, [
            'мини' => 'thumb',
            'миниатюра' => 'thumbnail',
        ]);
    }

    public function getBorder()
    {
        return $this->border;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getAlignment()
    {
        return $this->alignment;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function getLangtag()
    {
        return $this->langtag;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    private function normalize($value, array $map)
    {
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
}
