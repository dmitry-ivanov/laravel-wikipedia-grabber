<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext;

use Illuminate\Support\Str;

class WikitextImage extends Wikitext
{
    /**
     * The name.
     */
    protected string $name;

    /**
     * The type.
     */
    protected ?string $type = null;

    /**
     * The border.
     */
    protected ?string $border = null;

    /**
     * The location.
     */
    protected ?string $location = null;

    /**
     * The alignment.
     */
    protected ?string $alignment = null;

    /**
     * The size.
     */
    protected ?string $size = null;

    /**
     * The link.
     */
    protected ?string $link = null;

    /**
     * The alternative text.
     */
    protected ?string $alt = null;

    /**
     * The langtag.
     */
    protected ?string $langtag = null;

    /**
     * The page.
     */
    protected ?string $page = null;

    /**
     * The class.
     */
    protected ?string $class = null;

    /**
     * The caption.
     */
    protected ?string $caption = null;

    /**
     * Create a new instance of the WikitextImage.
     */
    public function __construct(string $body)
    {
        parent::__construct($body);

        $this->parse();
    }

    /**
     * Check whether an image is icon or not.
     */
    public function isIcon(): bool
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
     */
    protected function parse(): void
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
     */
    protected function strip(string $body): string
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
     */
    protected function isHandledTemplate(string $body): bool
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
     */
    protected function explode(string $body): array
    {
        $parts = explode('|', $body);
        $this->setName(array_shift($parts));

        return $parts;
    }

    /**
     * Handle the given value.
     */
    protected function handle(string $value): bool
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
     */
    public function getDescription(): string|null
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
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name.
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Check whether the given string is type or not.
     *
     * @noinspection PhpUnused
     */
    protected function isType(string $string): bool
    {
        return in_array($string, ['thumb', 'thumbnail', 'frame', 'framed', 'frameless'])
            || in_array($string, ['мини', 'миниатюра'])
            || preg_match('/^thumb(\s*)=/', $string) || preg_match('/^thumbnail(\s*)=/', $string);
    }

    /**
     * Get the type.
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @noinspection PhpUnused
     */
    protected function setType(string $type): void
    {
        $this->type = $this->normalize($type, [
            'мини' => 'thumb', 'миниатюра' => 'thumbnail',
        ]);
    }

    /**
     * Check whether the given string is border or not.
     *
     * @noinspection PhpUnused
     */
    protected function isBorder(string $string): bool
    {
        return $string == 'border';
    }

    /**
     * Get the border.
     */
    public function getBorder(): string|null
    {
        return $this->border;
    }

    /**
     * Set the border.
     *
     * @noinspection PhpUnused
     */
    protected function setBorder(string $border): void
    {
        $this->border = $border;
    }

    /**
     * Check whether the given string is location or not.
     *
     * @noinspection PhpUnused
     */
    protected function isLocation(string $string): bool
    {
        return in_array($string, ['right', 'left', 'center', 'none'])
            || in_array($string, ['справа', 'слева', 'центр'])
            || in_array($string, ['право', 'лево', 'середина']);
    }

    /**
     * Get the location.
     */
    public function getLocation(): string|null
    {
        return $this->location;
    }

    /**
     * Set the location.
     *
     * @noinspection PhpUnused
     */
    protected function setLocation(string $location): void
    {
        $this->location = $this->normalize($location, [
            'справа' => 'right', 'слева' => 'left', 'центр' => 'center',
            'право' => 'right', 'лево' => 'left', 'середина' => 'center',
        ]);
    }

    /**
     * Check whether the given string is alignment or not.
     *
     * @noinspection PhpUnused
     */
    protected function isAlignment(string $string): bool
    {
        return in_array($string, ['baseline', 'middle', 'sub', 'super', 'text-top', 'text-bottom', 'top', 'bottom']);
    }

    /**
     * Get the alignment.
     */
    public function getAlignment(): string|null
    {
        return $this->alignment;
    }

    /**
     * Set the alignment.
     *
     * @noinspection PhpUnused
     */
    protected function setAlignment(string $alignment): void
    {
        $this->alignment = $alignment;
    }

    /**
     * Check whether the given string is size or not.
     *
     * @noinspection PhpUnused
     */
    protected function isSize(string $string): bool
    {
        return $string === 'upright'
            || preg_match('/^upright(\s*)=/', $string)
            || preg_match('/^(\d+)(\s*)px$/', $string) || preg_match('/^x(\d+)px$/', $string) || preg_match('/^(\d+)x(\d+)px$/', $string)
            || preg_match('/^(\d+)(\s*)пкс$/', $string) || preg_match('/^x(\d+)пкс$/', $string) || preg_match('/^(\d+)x(\d+)пкс$/', $string);
    }

    /**
     * Get the size.
     */
    public function getSize(): string|null
    {
        return $this->size;
    }

    /**
     * Set the size.
     *
     * @noinspection PhpUnused
     */
    protected function setSize(string $size): void
    {
        $this->size = $size;
    }

    /**
     * Check whether the given string is link or not.
     *
     * @noinspection PhpUnused
     */
    protected function isLink(string $string): bool
    {
        return (bool) preg_match('/^link(\s*)=/', $string);
    }

    /**
     * Get the link.
     */
    public function getLink(): string|null
    {
        return $this->link;
    }

    /**
     * Set the link.
     *
     * @noinspection PhpUnused
     */
    protected function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * Check whether the given string is alt or not.
     *
     * @noinspection PhpUnused
     */
    protected function isAlt(string $string): bool
    {
        return preg_match('/^alt(\s*)=/', $string) || preg_match('/^альт(\s*)=/', $string);
    }

    /**
     * Get the alt.
     */
    public function getAlt(): string|null
    {
        return $this->alt;
    }

    /**
     * Set the alt.
     *
     * @noinspection PhpUnused
     */
    protected function setAlt(string $alt): void
    {
        $this->alt = $alt;
    }

    /**
     * Check whether the given string is langtag or not.
     *
     * @noinspection PhpUnused
     */
    protected function isLangtag(string $string): bool
    {
        return (bool) preg_match('/^lang(\s*)=/', $string);
    }

    /**
     * Get the langtag.
     */
    public function getLangtag(): string|null
    {
        return $this->langtag;
    }

    /**
     * Set the langtag.
     *
     * @noinspection PhpUnused
     */
    protected function setLangtag(string $langtag): void
    {
        $this->langtag = $langtag;
    }

    /**
     * Check whether the given string is page or not.
     *
     * @noinspection PhpUnused
     */
    protected function isPage(string $string): bool
    {
        return (bool) preg_match('/^page(\s*)=/', $string);
    }

    /**
     * Get the page.
     */
    public function getPage(): string|null
    {
        return $this->page;
    }

    /**
     * Set the page.
     *
     * @noinspection PhpUnused
     */
    protected function setPage(string $page): void
    {
        $this->page = $page;
    }

    /**
     * Check whether the given string is class or not.
     *
     * @noinspection PhpUnused
     */
    protected function isClass(string $string): bool
    {
        return (bool) preg_match('/^class(\s*)=/', $string);
    }

    /**
     * Get the class.
     */
    public function getClass(): string|null
    {
        return $this->class;
    }

    /**
     * Set the class.
     *
     * @noinspection PhpUnused
     */
    protected function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * Get the caption.
     */
    public function getCaption(): string|null
    {
        return $this->caption;
    }

    /**
     * Set the caption.
     */
    public function setCaption(string $caption): void
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
     */
    protected function isTextParameter(string $string): bool
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
     */
    protected function isSomeParameter(string $string): bool
    {
        return preg_match('/^(\S+)(\s*?)(\S*)(\s*?)=/', $string)
            || preg_match('/^(\d+)(\s*)%$/', $string);
    }

    /**
     * Check whether the given string is file name or not.
     *
     * @see https://www.mediawiki.org/wiki/Help:Images#Supported_media_types_for_images
     */
    protected function isFileName(string $string): bool
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
     */
    protected function normalize(string $value, array $map): string
    {
        return array_key_exists($value, $map) ? $map[$value] : $value;
    }
}
