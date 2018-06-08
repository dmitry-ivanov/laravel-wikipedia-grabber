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
    protected $page;
    protected $class;
    protected $caption;

    public function __construct($body)
    {
        parent::__construct($body);

        $this->parse();
    }

    /**
     * @see https://www.mediawiki.org/wiki/Help:Images#Syntax
     * @see https://en.wikipedia.org/wiki/Wikipedia:Extended_image_syntax
     * @see https://ru.wikipedia.org/wiki/Википедия:Иллюстрирование
     */
    protected function parse()
    {
        $body = trim($this->body);

        $body = $this->strip($body);
        $body = $this->plain($body);
        $parts = $this->explode($body);

        foreach ($parts as $part) {
            if ($this->handle($part)) {
                continue;
            }

            $this->setCaption($part);
        }
    }

    protected function strip($body)
    {
        if (starts_with($body, '[[') && ends_with($body, ']]')) {
            $body = str_replace_first('[[', '', $body);
            $body = str_replace_last(']]', '', $body);
        }

        if ($this->isHandledTemplate($body)) {
            $body = str_replace_first('{{', '', $body);
            $body = str_replace_last('}}', '', $body);
        }

        return $body;
    }

    protected function isHandledTemplate($body)
    {
        $body = mb_strtolower($body, 'utf-8');

        $templates = collect([
            'annotated image', 'описанное изображение',
            'css image crop', 'часть изображения',
            'multiple image', 'кратное изображение',
            'фоторяд', 'фотоколонка',
            'wide image', 'панорама',
            'photomontage', 'фотомонтаж',
            'image frame', 'рамка в стиле миниатюры',
        ])->map(function ($template) {
            return "{{{$template}";
        })->toArray();

        return starts_with($body, $templates) && ends_with($body, '}}');
    }

    protected function explode($body)
    {
        $parts = explode('|', $body);
        $this->setName(array_shift($parts));

        return $parts;
    }

    protected function handle($value)
    {
        $part = mb_strtolower(trim($value), 'utf-8');

        $fields = ['type', 'border', 'location', 'alignment', 'size', 'link', 'alt', 'langtag', 'page', 'class'];
        foreach ($fields as $field) {
            $is = camel_case("is_{$field}");
            $set = camel_case("set_{$field}");
            if ($this->{$is}($part)) {
                $this->{$set}(
                    ($field == 'alt') ? $value : $part
                );
                return true;
            }
        }

        if ($this->isTextParameter($part)) {
            $this->setCaption(last(explode('=', $value)));
            return true;
        }

        if ($this->isSomeParameter($part)) {
            return true;
        }

        if ($this->isFileName($part)) {
            return true;
        }

        return false;
    }

    public function getDescription()
    {
        if ($caption = $this->getCaption()) {
            return $caption;
        }

        if ($alt = $this->getAlt()) {
            return trim(last(explode('=', $alt)));
        }

        return null;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setName($name)
    {
        $this->name = $name;
    }

    protected function isType($string)
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || in_array($string, ['мини', 'миниатюра'])
            || preg_match('/^thumb(\s*)=/', $string) || preg_match('/^thumbnail(\s*)=/', $string);
    }

    public function getType()
    {
        return $this->type;
    }

    protected function setType($type)
    {
        $this->type = $this->normalize($type, [
            'мини' => 'thumb', 'миниатюра' => 'thumbnail',
        ]);
    }

    protected function isBorder($string)
    {
        return ($string == 'border');
    }

    public function getBorder()
    {
        return $this->border;
    }

    protected function setBorder($border)
    {
        $this->border = $border;
    }

    protected function isLocation($string)
    {
        return in_array($string, ['right', 'left', 'center', 'none'])
            || in_array($string, ['справа', 'слева', 'центр'])
            || in_array($string, ['право', 'лево', 'середина']);
    }

    public function getLocation()
    {
        return $this->location;
    }

    protected function setLocation($location)
    {
        $this->location = $this->normalize($location, [
            'справа' => 'right', 'слева' => 'left', 'центр' => 'center',
            'право' => 'right', 'лево' => 'left', 'середина' => 'center',
        ]);
    }

    protected function isAlignment($string)
    {
        return in_array($string, ['baseline', 'middle', 'sub', 'super', 'text-top', 'text-bottom', 'top', 'bottom']);
    }

    public function getAlignment()
    {
        return $this->alignment;
    }

    protected function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    protected function isSize($string)
    {
        return in_array($string, ['upright'])
            || preg_match('/^upright(\s*)=/', $string)
            || preg_match('/^(\d+)(\s*)px$/', $string) || preg_match('/^x(\d+)px$/', $string) || preg_match('/^(\d+)x(\d+)px$/', $string)
            || preg_match('/^(\d+)(\s*)пкс$/', $string) || preg_match('/^x(\d+)пкс$/', $string) || preg_match('/^(\d+)x(\d+)пкс$/', $string);
    }

    public function getSize()
    {
        return $this->size;
    }

    protected function setSize($size)
    {
        $this->size = $size;
    }

    protected function isLink($string)
    {
        return preg_match('/^link(\s*)=/', $string);
    }

    public function getLink()
    {
        return $this->link;
    }

    protected function setLink($link)
    {
        $this->link = $link;
    }

    protected function isAlt($string)
    {
        return preg_match('/^alt(\s*)=/', $string) || preg_match('/^альт(\s*)=/', $string);
    }

    public function getAlt()
    {
        return $this->alt;
    }

    protected function setAlt($alt)
    {
        $this->alt = $alt;
    }

    protected function isLangtag($string)
    {
        return preg_match('/^lang(\s*)=/', $string);
    }

    public function getLangtag()
    {
        return $this->langtag;
    }

    protected function setLangtag($langtag)
    {
        $this->langtag = $langtag;
    }

    protected function isPage($string)
    {
        return preg_match('/^page(\s*)=/', $string);
    }

    public function getPage()
    {
        return $this->page;
    }

    protected function setPage($page)
    {
        $this->page = $page;
    }

    protected function isClass($string)
    {
        return preg_match('/^class(\s*)=/', $string);
    }

    public function getClass()
    {
        return $this->class;
    }

    protected function setClass($class)
    {
        $this->class = $class;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function setCaption($caption)
    {
        $this->caption = trim($caption);
    }

    /**
     * @see https://en.wikipedia.org/wiki/Template:Annotated_image - caption
     * @see https://ru.wikipedia.org/wiki/Шаблон:Описанное_изображение - caption
     * @see https://en.wikipedia.org/wiki/Template:CSS_image_crop - description
     * @see https://ru.wikipedia.org/wiki/Шаблон:Часть_изображения - подпись
     * @see https://en.wikipedia.org/wiki/Template:Multiple_image - footer
     * @see https://ru.wikipedia.org/wiki/Шаблон:Кратное_изображение - подпись
     * @see https://ru.wikipedia.org/wiki/Шаблон:Фоторяд - текст
     * @see https://ru.wikipedia.org/wiki/Шаблон:Фотоколонка - текст
     * @see https://en.wikipedia.org/wiki/Template:Photomontage - text
     * @see https://ru.wikipedia.org/wiki/Шаблон:Фотомонтаж - text
     * @see https://en.wikipedia.org/wiki/Template:Image_frame - caption
     * @see https://ru.wikipedia.org/wiki/Шаблон:Image_frame - заголовок
     * @see https://ru.wikipedia.org/w/index.php?title=Шаблон:Рамка_в_стиле_миниатюры&redirect=no - заголовок
     */
    protected function isTextParameter($string)
    {
        return preg_match('/^text(\s*)=(.+?)/', $string) || preg_match('/^текст(\s*)=(.+?)/', $string)
            || preg_match('/^description(\s*)=(.+?)/', $string) || preg_match('/^подпись(\s*)=(.+?)/', $string)
            || preg_match('/^footer(\s*)=(.+?)/', $string)
            || preg_match('/^caption(\s*)=(.+?)/', $string) || preg_match('/^заголовок(\s*)=(.+?)/', $string);
    }

    protected function isSomeParameter($string)
    {
        return preg_match('/^(\S+)(\s*?)(\S*)(\s*?)=/', $string)
            || preg_match('/^(\d+)(\s*)%$/', $string);
    }

    /**
     * @see https://www.mediawiki.org/wiki/Help:Images#Supported_media_types_for_images
     */
    protected function isFileName($string)
    {
        $extensions = collect([
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'ogg', 'oga', 'ogv', 'pdf', 'djvu', 'tiff',
        ])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return ends_with($string, $extensions);
    }

    private function normalize($value, array $map)
    {
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
}
