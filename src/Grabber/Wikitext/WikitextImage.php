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

        if ($this->isTextParameter($part)) {
            $this->caption = rtrim(last(explode('=', $part)), '}');
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

    protected function isType($string)
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || in_array($string, ['мини', 'миниатюра'])
            || starts_with($string, ['thumb=', 'thumbnail=']);
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
            || starts_with($string, ['upright='])
            || preg_match('/(\d+)px/', $string) || preg_match('/x(\d+)px/', $string) || preg_match('/(\d+)x(\d+)px/', $string)
            || preg_match('/(\d+)пкс/', $string) || preg_match('/x(\d+)пкс/', $string) || preg_match('/(\d+)x(\d+)пкс/', $string);
    }

    public function getSize()
    {
        return $this->size;
    }

    protected function setSize($size)
    {
        $size = str_replace_last('пкс', 'px', $size);

        $this->size = $size;
    }

    protected function isLink($string)
    {
        return starts_with($string, ['link=']);
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
        return starts_with($string, 'alt=')
            || starts_with($string, 'альт=');
    }

    public function getAlt()
    {
        return $this->alt;
    }

    protected function setAlt($alt)
    {
        $alt = str_replace_first('альт=', 'alt=', $alt);

        $this->alt = $alt;
    }

    protected function isLangtag($string)
    {
        return starts_with($string, ['lang=']);
    }

    public function getLangtag()
    {
        return $this->langtag;
    }

    protected function setLangtag($langtag)
    {
        $this->langtag = $langtag;
    }

    public function getCaption()
    {
        return $this->caption;
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
        $string = mb_strtolower($string, 'utf-8');

        return preg_match('/text=(.+?)/', $string) || preg_match('/текст=(.+?)/', $string)
            || preg_match('/description=(.+?)/', $string) || preg_match('/подпись=(.+?)/', $string)
            || preg_match('/footer=(.+?)/', $string)
            || preg_match('/caption=(.+?)/', $string) || preg_match('/заголовок=(.+?)/', $string);
    }

    protected function isSomeParameter($string)
    {
        return preg_match('/(.+?)=(.+?)/', $string) || preg_match('/^(\d+)%$/', $string);
    }

    protected function isFileName($string)
    {
        $extensions = collect(['jpg', 'jpeg', 'png', 'svg'])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return ends_with($string, $extensions);
    }

    private function normalize($value, array $map)
    {
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
}
