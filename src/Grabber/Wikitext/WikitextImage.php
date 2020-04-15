<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

use Illuminate\Support\Str;

class WikitextImage extends Wikitext
{
    /**
     * The name.
     *
     * @var string
     */
    protected $name;

    /**
     * The type.
     *
     * @var string
     */
    protected $type;

    /**
     * The border.
     *
     * @var string
     */
    protected $border;

    /**
     * The location.
     *
     * @var string
     */
    protected $location;

    /**
     * The alignment.
     *
     * @var string
     */
    protected $alignment;

    /**
     * The size.
     *
     * @var string
     */
    protected $size;

    /**
     * The link.
     *
     * @var string
     */
    protected $link;

    /**
     * The alternative text.
     *
     * @var string
     */
    protected $alt;

    /**
     * The langtag.
     *
     * @var string
     */
    protected $langtag;

    /**
     * The page.
     *
     * @var string
     */
    protected $page;

    /**
     * The class.
     *
     * @var string
     */
    protected $class;

    /**
     * The caption.
     *
     * @var string
     */
    protected $caption;

    /**
     * Create a new instance of the WikitextImage.
     *
     * @param string $body
     * @return void
     */
    public function __construct(string $body)
    {
        parent::__construct($body);

        $this->parse();
    }

    /**
     * Check whether an image is icon or not.
     *
     * @return bool
     */
    public function isIcon()
    {
        $size = $this->getSize();
        if (empty($size) || Str::contains($size, 'upright') || !preg_match('/\d+/', $size, $matches)) {
            return false;
        }

        $size = (int) head($matches);

        return $size <= 50;
    }

    /**
     * Parse the wikitext image.
     *
     * @see https://www.mediawiki.org/wiki/Help:Images#Syntax
     * @see https://en.wikipedia.org/wiki/Wikipedia:Extended_image_syntax
     * @see https://ru.wikipedia.org/wiki/Википедия:Иллюстрирование
     *
     * @return void
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

    /**
     * Strip the given body.
     *
     * @param string $body
     * @return string
     */
    protected function strip(string $body)
    {
        if (Str::startsWith($body, '[[') && Str::endsWith($body, ']]')) {
            $body = Str::replaceFirst('[[', '', $body);
            $body = Str::replaceLast(']]', '', $body);
        }

        if ($this->isHandledTemplate($body)) {
            $body = Str::replaceFirst('{{', '', $body);
            $body = Str::replaceLast('}}', '', $body);
        }

        return $body;
    }

    /**
     * Check whether the given body is handled template or not.
     *
     * @param string $body
     * @return bool
     */
    protected function isHandledTemplate(string $body)
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
            'listen', 'spoken', 'audio', 'pronunciation',
            'sample', 'музыкальный отрывок стиля', 'семпл', 'музос',
        ])->map(function ($template) {
            return "{{{$template}";
        })->toArray();

        return Str::startsWith($body, $templates) && Str::endsWith($body, '}}');
    }

    /**
     * Explode the given body.
     *
     * @param string $body
     * @return array|string[]
     */
    protected function explode(string $body)
    {
        $parts = explode('|', $body);
        $this->setName(array_shift($parts));

        return $parts;
    }

    /**
     * Handle the given value.
     *
     * @param string $value
     * @return bool
     */
    protected function handle(string $value)
    {
        $part = mb_strtolower(trim($value), 'utf-8');

        $fields = ['type', 'border', 'location', 'alignment', 'size', 'link', 'alt', 'langtag', 'page', 'class'];
        foreach ($fields as $field) {
            $is = Str::camel("is_{$field}");
            $set = Str::camel("set_{$field}");
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

    /**
     * Get the description.
     *
     * @return string|null
     */
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

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name
     * @return void
     */
    protected function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Check whether the given string is type or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isType(string $string)
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || in_array($string, ['мини', 'миниатюра'])
            || preg_match('/^thumb(\s*)=/', $string) || preg_match('/^thumbnail(\s*)=/', $string);
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @param string $type
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setType(string $type)
    {
        $this->type = $this->normalize($type, [
            'мини' => 'thumb', 'миниатюра' => 'thumbnail',
        ]);
    }

    /**
     * Check whether the given string is border or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isBorder(string $string)
    {
        return $string == 'border';
    }

    /**
     * Get the border.
     *
     * @return string
     */
    public function getBorder()
    {
        return $this->border;
    }

    /**
     * Set the border.
     *
     * @param string $border
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setBorder(string $border)
    {
        $this->border = $border;
    }

    /**
     * Check whether the given string is location or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isLocation(string $string)
    {
        return in_array($string, ['right', 'left', 'center', 'none'])
            || in_array($string, ['справа', 'слева', 'центр'])
            || in_array($string, ['право', 'лево', 'середина']);
    }

    /**
     * Get the location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the location.
     *
     * @param string $location
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setLocation(string $location)
    {
        $this->location = $this->normalize($location, [
            'справа' => 'right', 'слева' => 'left', 'центр' => 'center',
            'право' => 'right', 'лево' => 'left', 'середина' => 'center',
        ]);
    }

    /**
     * Check whether the given string is alignment or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isAlignment(string $string)
    {
        return in_array($string, ['baseline', 'middle', 'sub', 'super', 'text-top', 'text-bottom', 'top', 'bottom']);
    }

    /**
     * Get the alignment.
     *
     * @return string
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Set the alignment.
     *
     * @param string $alignment
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setAlignment(string $alignment)
    {
        $this->alignment = $alignment;
    }

    /**
     * Check whether the given string is size or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isSize(string $string)
    {
        return in_array($string, ['upright'])
            || preg_match('/^upright(\s*)=/', $string)
            || preg_match('/^(\d+)(\s*)px$/', $string) || preg_match('/^x(\d+)px$/', $string) || preg_match('/^(\d+)x(\d+)px$/', $string)
            || preg_match('/^(\d+)(\s*)пкс$/', $string) || preg_match('/^x(\d+)пкс$/', $string) || preg_match('/^(\d+)x(\d+)пкс$/', $string);
    }

    /**
     * Get the size.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the size.
     *
     * @param string $size
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setSize(string $size)
    {
        $this->size = $size;
    }

    /**
     * Check whether the given string is link or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isLink(string $string)
    {
        return (bool) preg_match('/^link(\s*)=/', $string);
    }

    /**
     * Get the link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the link.
     *
     * @param string $link
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * Check whether the given string is alt or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isAlt(string $string)
    {
        return preg_match('/^alt(\s*)=/', $string) || preg_match('/^альт(\s*)=/', $string);
    }

    /**
     * Get the alt.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set the alt.
     *
     * @param string $alt
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setAlt(string $alt)
    {
        $this->alt = $alt;
    }

    /**
     * Check whether the given string is langtag or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isLangtag(string $string)
    {
        return (bool) preg_match('/^lang(\s*)=/', $string);
    }

    /**
     * Get the langtag.
     *
     * @return string
     */
    public function getLangtag()
    {
        return $this->langtag;
    }

    /**
     * Set the langtag.
     *
     * @param string $langtag
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setLangtag(string $langtag)
    {
        $this->langtag = $langtag;
    }

    /**
     * Check whether the given string is page or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isPage(string $string)
    {
        return (bool) preg_match('/^page(\s*)=/', $string);
    }

    /**
     * Get the page.
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the page.
     *
     * @param string $page
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setPage(string $page)
    {
        $this->page = $page;
    }

    /**
     * Check whether the given string is class or not.
     *
     * @param string $string
     * @return bool
     *
     * @noinspection PhpUnused
     */
    protected function isClass(string $string)
    {
        return (bool) preg_match('/^class(\s*)=/', $string);
    }

    /**
     * Get the class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the class.
     *
     * @param string $class
     * @return void
     *
     * @noinspection PhpUnused
     */
    protected function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * Get the caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set the caption.
     *
     * @param string $caption
     * @return void
     */
    public function setCaption(string $caption)
    {
        $this->caption = trim($caption);
    }

    /**
     * Check whether the given string is text parameter or not.
     *
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
     * @see https://en.wikipedia.org/wiki/Template:Listen - title, description
     * @see https://ru.wikipedia.org/wiki/Шаблон:Listen - название, описание
     * @see https://ru.wikipedia.org/w/index.php?title=Шаблон:Sample&redirect=no - пояснения
     * @see https://ru.wikipedia.org/wiki/Шаблон:Музыкальный_отрывок_стиля - пояснения
     * @see https://ru.wikipedia.org/wiki/Шаблон:Семпл - пояснения
     * @see https://ru.wikipedia.org/w/index.php?title=Шаблон:МузОС&redirect=no - пояснения
     *
     * @param string $string
     * @return bool
     */
    protected function isTextParameter(string $string)
    {
        return preg_match('/^text(\s*)=(.+?)/', $string) || preg_match('/^текст(\s*)=(.+?)/', $string)
            || preg_match('/^caption(\s*)=(.+?)/', $string) || preg_match('/^заголовок(\s*)=(.+?)/', $string)
            || preg_match('/^title(\s*)=(.+?)/', $string) || preg_match('/^название(\s*)=(.+?)/', $string)
            || preg_match('/^description(\s*)=(.+?)/', $string) || preg_match('/^описание(\s*)=(.+?)/', $string)
            || preg_match('/^footer(\s*)=(.+?)/', $string) || preg_match('/^подпись(\s*)=(.+?)/', $string)
            || preg_match('/^пояснения(\s*)=(.+?)/', $string);
    }

    /**
     * Check whether the given string is some parameter or not.
     *
     * @param string $string
     * @return bool
     */
    protected function isSomeParameter(string $string)
    {
        return preg_match('/^(\S+)(\s*?)(\S*)(\s*?)=/', $string)
            || preg_match('/^(\d+)(\s*)%$/', $string);
    }

    /**
     * Check whether the given string is file name or not.
     *
     * @see https://www.mediawiki.org/wiki/Help:Images#Supported_media_types_for_images
     *
     * @param string $string
     * @return bool
     */
    protected function isFileName(string $string)
    {
        $string = mb_strtolower($string, 'utf-8');

        $extensions = collect([
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'ogg', 'oga', 'ogv', 'pdf', 'djvu', 'tiff', 'mp3', 'wav', 'mp4', 'webm',
        ])->map(function ($ext) {
            return ".{$ext}";
        })->toArray();

        return Str::endsWith($string, $extensions);
    }

    /**
     * Normalize the given value according to the given map.
     *
     * @param string $value
     * @param array $map
     * @return string
     */
    protected function normalize(string $value, array $map)
    {
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
}
