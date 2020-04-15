<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Component\Section\Gallery;
use Illuminated\Wikipedia\Grabber\Parser\SectionsParser;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\LocaleFile;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineFile;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineTemplate;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\Underscores;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\DoubleImageTemplate;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\ListenTemplate;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\MultipleImageTemplate;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;
use Illuminated\Wikipedia\Grabber\Wikitext\WikitextImage;

class SectionsAddImages
{
    /**
     * The sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sections;

    /**
     * The wikitext.
     *
     * @var string
     */
    protected $wikitext;

    /**
     * The main image data.
     *
     * @var array
     */
    protected $mainImage;

    /**
     * The images.
     *
     * @var array
     */
    protected $images;

    /**
     * The wikitext sections.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $wikitextSections;

    /**
     * Create a new instance of the pipe.
     *
     * @param \Illuminate\Support\Collection $sections
     * @param array|null $imagesResponseData
     * @return void
     */
    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        $this->sections = $sections;

        if (empty($imagesResponseData)) {
            return;
        }

        $this->wikitext = $this->normalizeWikitext($imagesResponseData['wikitext']);
        $this->mainImage = $imagesResponseData['main_image'];
        $this->images = $this->imagesFromResponse($imagesResponseData['images']);
    }

    /**
     * Execute the pipe.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pipe()
    {
        if ($this->noImages()) {
            return $this->sections;
        }

        foreach ($this->sections as $section) {
            if ($section->isMain() && !empty($this->mainImage)) {
                $section->setImages($this->createMainObject());
            }

            $wikitextSection = $this->getWikitextSectionFor($section);
            if (empty($wikitextSection)) {
                continue;
            }

            $sectionImages = $this->getUsedImages($wikitextSection->getBody(), $this->images);
            if (empty($sectionImages)) {
                continue;
            }

            $sectionImages = $this->filterByExtensions($wikitextSection, $sectionImages);
            if (empty($sectionImages)) {
                continue;
            }

            $objects = $this->createObjects($wikitextSection, $sectionImages);
            $section->addImages($objects['images']);
            $section->setGallery($objects['gallery']);

            $this->freeUsedImages($sectionImages);
        }

        return $this->sections;
    }

    /**
     * Check whether there are no images.
     *
     * @return bool
     */
    protected function noImages()
    {
        return empty($this->mainImage)
            && empty($this->images);
    }

    /**
     * Compose images from the response data.
     *
     * @param array $imagesFromResponse
     * @return array
     */
    protected function imagesFromResponse(array $imagesFromResponse)
    {
        $images = $this->normalizeImages($imagesFromResponse);
        $images = $this->getUsedImages($this->wikitext, $images);
        $images = $this->skipMainImage($images);

        return $images;
    }

    /**
     * Get used on the page images.
     *
     * @param string $wikitext
     * @param array $images
     * @return array
     */
    protected function getUsedImages(string $wikitext, array $images)
    {
        return collect($images)->filter(function (array $image) use ($wikitext) {
            $file = last(explode(':', $image['title']));
            return $this->isFileUsed($wikitext, $file);
        })->toArray();
    }

    /**
     * Skip the main image.
     *
     * @param array $images
     * @return array
     */
    protected function skipMainImage(array $images)
    {
        return collect($images)->filter(function (array $image) {
            return !(Arr::get($image, 'imageinfo.0.url') == $this->mainImage['original']['source']);
        })->toArray();
    }

    /**
     * Check whether the file is used or not.
     *
     * @param string $wikitext
     * @param string $file
     * @return bool
     */
    protected function isFileUsed(string $wikitext, string $file)
    {
        return Str::contains($wikitext, $file);
    }

    /**
     * Filter images according to supported extensions.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $wikitextSection
     * @param array $images
     * @return array
     */
    protected function filterByExtensions(Section $wikitextSection, array $images)
    {
        if (!$wikitextSection->isMain()) {
            return $images;
        }

        return collect($images)->filter(function (array $image) {
            return Str::endsWith(
                mb_strtolower($image['title'], 'utf-8'),
                ['jpg', 'jpeg', 'ogg', 'oga', 'ogv', 'pdf', 'djvu', 'tiff', 'mp3', 'wav', 'mp4', 'webm']
            );
        })->toArray();
    }

    /**
     * Create the main object.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function createMainObject()
    {
        return collect([
            new Image(
                $this->mainImage['thumbnail']['source'],
                $this->mainImage['thumbnail']['width'],
                $this->mainImage['thumbnail']['height'],
                $this->mainImage['original']['source'],
                'right',
                $this->getMainSection()->getTitle()
            ),
        ]);
    }

    /**
     * Create objects for the given section.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $wikitextSection
     * @param array $images
     * @return array
     */
    protected function createObjects(Section $wikitextSection, array $images)
    {
        $objects = ['gallery' => collect(), 'images' => collect()];

        foreach ($images as $image) {
            $wikitext = $this->getImageWikitext($wikitextSection, $image);
            $object = $this->createObject($wikitext, $image);
            if (empty($object)) {
                continue;
            }

            $collection = $this->isGalleryImage($wikitext) ? $objects['gallery'] : $objects['images'];
            $collection->push($object);
        }

        if ($objects['gallery']->isNotEmpty()) {
            $pure = (new Gallery)->validate($objects['gallery']);
            $objects['gallery'] = $pure['gallery'];
            $objects['images'] = $objects['images']->merge($pure['not_gallery']);
        }

        return $objects;
    }

    /**
     * Create an object.
     *
     * @param string $imageWikitext
     * @param array $image
     * @return \Illuminated\Wikipedia\Grabber\Component\Image|false
     */
    protected function createObject(string $imageWikitext, array $image)
    {
        $imageInfo = head($image['imageinfo']);

        $url = $imageInfo['thumburl'];
        $width = $imageInfo['thumbwidth'];
        $height = $imageInfo['thumbheight'];
        $originalUrl = $imageInfo['url'];
        $mime = $imageInfo['mime'];

        $image = new WikitextImage($imageWikitext);
        $position = (string) $image->getLocation();
        $description = (string) $image->getDescription();

        if ($image->isIcon()) {
            return false;
        }

        return new Image($url, $width, $height, $originalUrl, $position, $description, $mime);
    }

    /**
     * Get image wikitext.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $wikitextSection
     * @param array $image
     * @return string
     */
    protected function getImageWikitext(Section $wikitextSection, array $image)
    {
        $title = $image['title'];
        $file = last(explode(':', $title));

        $line = $this->getImageWikitextLine($wikitextSection->getBody(), $title, $file);

        $openTag = "[[{$title}";
        if (!Str::contains($line, $openTag)) {
            if ($this->isDoubleImageTemplate($line)) {
                return (new DoubleImageTemplate($line))->extract($file);
            }

            if ($this->isMultipleImageTemplate($line)) {
                $line = (new MultipleImageTemplate($line))->extract($file);
                if ($this->isListenTemplate($line)) {
                    $line = (new ListenTemplate($line))->transform();
                }

                return $line;
            }

            if ($this->isAudioTemplate($line, $image, $matches)) {
                return head($matches);
            }

            return $line;
        }

        $placeholder = '/!! IWG-FILE-TITLE !!/';
        $line = Str::replaceFirst($openTag, $placeholder, $line);
        $line = (new Wikitext($line))->removeLinks();
        $line = Str::replaceFirst($placeholder, $openTag, $line);

        $title = preg_quote($title, '/');
        if (preg_match("/\[\[{$title}.*?\]\]/", $line, $matches)) {
            $wikitext = head($matches);

            if ($this->isGrayTable($line)) {
                $wikitext = $this->forceGalleryDisplaying($wikitext);
            }

            $line = $wikitext;
        }

        return $line;
    }

    /**
     * Get the image's wikitext line.
     *
     * @param string $wikitext
     * @param string $title
     * @param string $file
     * @return string
     */
    protected function getImageWikitextLine(string $wikitext, string $title, string $file)
    {
        $lines = collect(preg_split("/\r\n|\n|\r/", $wikitext));

        $line = $lines->first(function ($line) use ($title) {
            return $this->isFileUsed($line, $title);
        });

        if (!empty($line)) {
            return $line;
        }

        return $lines->first(function ($line) use ($file) {
            return $this->isFileUsed($line, $file);
        });
    }

    /**
     * Check whether the given image wikitext is a gallery image or not.
     *
     * @param string $imageWikitext
     * @return bool
     */
    protected function isGalleryImage(string $imageWikitext)
    {
        return !(Str::startsWith($imageWikitext, '[[') && Str::endsWith($imageWikitext, ']]'));
    }

    /**
     * Force gallery displaying for the given image wikitext.
     *
     * @param string $imageWikitext
     * @return string
     */
    protected function forceGalleryDisplaying(string $imageWikitext)
    {
        $imageWikitext = Str::replaceFirst('[[', '', $imageWikitext);
        $imageWikitext = Str::replaceLast(']]', '', $imageWikitext);

        return $imageWikitext;
    }

    /**
     * Check whether the given line is a "double image" template or not.
     *
     * @param string $line
     * @return bool
     */
    protected function isDoubleImageTemplate(string $line)
    {
        return Str::startsWith(
            mb_strtolower($line, 'utf-8'),
            ['{{double image', '{{сдвоенное изображение']
        );
    }

    /**
     * Check whether the given line is a "listen" template or not.
     *
     * @param string $line
     * @return bool
     */
    protected function isListenTemplate(string $line)
    {
        return Str::startsWith(mb_strtolower($line, 'utf-8'), '{{listen');
    }

    /**
     * Check whether the given line is a "multiple image" template or not.
     *
     * @param string $line
     * @return bool
     */
    protected function isMultipleImageTemplate(string $line)
    {
        return Str::startsWith(
            mb_strtolower($line, 'utf-8'),
            ['{{multiple image', '{{кратное изображение', '{{фотоколонка', '{{listen']
        );
    }

    /**
     * Check whether the given line is an "audio" template or not.
     *
     * @param string $line
     * @param array $image
     * @param mixed $matches
     * @return bool
     */
    protected function isAudioTemplate(string $line, array $image, &$matches)
    {
        $file = last(explode(':', $image['title']));
        $file = preg_quote($file, '/');

        return (bool) preg_match("/\{\{(audio|pronunciation).*?\|{$file}\|.*?\}\}/i", $line, $matches);
    }

    /**
     * Check whether the given line is a "gray table" or not.
     *
     * @param string $line
     * @return bool
     */
    protected function isGrayTable(string $line)
    {
        return (bool) preg_match('/^(\s*\|\s*)width(\s*)=/i', $line) || preg_match('/^(\s*\|\s*)align(\s*)=/i', $line);
    }

    /**
     * Free used images.
     *
     * @param array $usedImages
     * @return void
     */
    protected function freeUsedImages(array $usedImages)
    {
        $usedImages = Arr::pluck($usedImages, 'title');
        $this->images = collect($this->images)->filter(function (array $image) use ($usedImages) {
            return !in_array($image['title'], $usedImages);
        })->toArray();
    }

    /**
     * Get wikitext section for the given section.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $section
     * @return \Illuminated\Wikipedia\Grabber\Component\Section|null
     */
    protected function getWikitextSectionFor(Section $section)
    {
        $wikitextSections = $this->getWikitextSections();

        return $wikitextSections->first(function (Section $wikiSection) use ($section) {
            return ($wikiSection->getTitle() == $section->getTitle())
                && ($wikiSection->getLevel() == $section->getLevel());
        });
    }

    /**
     * Get wikitext sections.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getWikitextSections()
    {
        if (empty($this->wikitextSections)) {
            $parser = new SectionsParser($this->getMainSection()->getTitle(), $this->wikitext);
            $this->wikitextSections = $parser->sections();
            $this->wikitextSections->each(function (Section $section) {
                $this->normalizeSection($section);
            });
        }

        return $this->wikitextSections;
    }

    /**
     * Normalize images.
     *
     * @param array $images
     * @return array
     */
    protected function normalizeImages(array $images)
    {
        return collect($images)->map(function ($image) {
            return $this->normalizeImage($image);
        })->toArray();
    }

    /**
     * Normalize image.
     *
     * @param array $image
     * @return array
     */
    protected function normalizeImage(array $image)
    {
        $image['title'] = (new LocaleFile)->normalize($image['title']);
        $image['title'] = (new Underscores)->normalize($image['title']);

        return $image;
    }

    /**
     * Normalize wikitext.
     *
     * @param string $wikitext
     * @return string
     */
    protected function normalizeWikitext(string $wikitext)
    {
        $wikitext = (new LocaleFile)->normalize($wikitext);
        $wikitext = (new Underscores)->normalize($wikitext);

        return $wikitext;
    }

    /**
     * Normalize section.
     *
     * @param \Illuminated\Wikipedia\Grabber\Component\Section $section
     * @return void
     */
    protected function normalizeSection(Section $section)
    {
        $body = $section->getBody();

        $body = (new MultilineFile)->flatten($body);
        $body = (new MultilineTemplate)->flatten($body);

        $section->setBody($body);
    }

    /**
     * Get the main section.
     *
     * @return \Illuminated\Wikipedia\Grabber\Component\Section
     */
    protected function getMainSection()
    {
        return $this->sections->first(function (Section $section) {
            return $section->isMain();
        });
    }
}
