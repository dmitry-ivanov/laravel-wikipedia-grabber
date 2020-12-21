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
    protected $sections;
    protected $wikitext;
    protected $mainImage;
    protected $images;
    protected $wikitextSections;

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

    protected function noImages()
    {
        return empty($this->mainImage) && empty($this->images);
    }

    protected function imagesFromResponse(array $imagesFromResponse)
    {
        $images = $this->normalizeImages($imagesFromResponse);
        $images = $this->getUsedImages($this->wikitext, $images);
        $images = $this->skipMainImage($images);

        return $images;
    }

    protected function getUsedImages($wikitext, array $images)
    {
        return collect($images)->filter(function (array $image) use ($wikitext) {
            $file = last(explode(':', $image['title']));
            return $this->isFileUsed($wikitext, $file);
        })->toArray();
    }

    protected function skipMainImage(array $images)
    {
        return collect($images)->filter(function (array $image) {
            return !(Arr::get($image, 'imageinfo.0.url') == $this->mainImage['original']['source']);
        })->toArray();
    }

    protected function isFileUsed($wikitext, $file)
    {
        return Str::contains($wikitext, $file);
    }

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

    protected function createObject($imageWikitext, array $image)
    {
        $imageInfo = head($image['imageinfo']);

        $mime = $imageInfo['mime'];
        $url = $imageInfo['thumburl'];
        $width = $imageInfo['thumbwidth'];
        $height = $imageInfo['thumbheight'];
        $originalUrl = $imageInfo['url'];

        $image = new WikitextImage($imageWikitext);
        $position = $image->getLocation();
        $description = $image->getDescription();

        if ($image->isIcon()) {
            return false;
        }

        return new Image($url, $width, $height, $originalUrl, $position, $description, $mime);
    }

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

    protected function getImageWikitextLine($wikitext, $title, $file)
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

    protected function isGalleryImage($imageWikitext)
    {
        return !(Str::startsWith($imageWikitext, '[[') && Str::endsWith($imageWikitext, ']]'));
    }

    protected function forceGalleryDisplaying($imageWikitext)
    {
        $imageWikitext = Str::replaceFirst('[[', '', $imageWikitext);
        $imageWikitext = Str::replaceLast(']]', '', $imageWikitext);

        return $imageWikitext;
    }

    protected function isDoubleImageTemplate($line)
    {
        return Str::startsWith(
            mb_strtolower($line, 'utf-8'),
            ['{{double image', '{{сдвоенное изображение']
        );
    }

    protected function isListenTemplate($line)
    {
        return Str::startsWith(mb_strtolower($line, 'utf-8'), '{{listen');
    }

    protected function isMultipleImageTemplate($line)
    {
        return Str::startsWith(
            mb_strtolower($line, 'utf-8'),
            ['{{multiple image', '{{кратное изображение', '{{фотоколонка', '{{listen']
        );
    }

    protected function isAudioTemplate($line, array $image, &$matches)
    {
        $file = last(explode(':', $image['title']));
        $file = preg_quote($file, '/');

        return preg_match("/\{\{(audio|pronunciation).*?\|{$file}\|.*?\}\}/i", $line, $matches);
    }

    protected function isGrayTable($line)
    {
        return preg_match('/^(\s*\|\s*)width(\s*)=/i', $line) || preg_match('/^(\s*\|\s*)align(\s*)=/i', $line);
    }

    protected function freeUsedImages(array $usedImages)
    {
        $usedImages = Arr::pluck($usedImages, 'title');
        $this->images = collect($this->images)->filter(function (array $image) use ($usedImages) {
            return !in_array($image['title'], $usedImages);
        })->toArray();
    }

    protected function getWikitextSectionFor(Section $section)
    {
        $wikitextSections = $this->getWikitextSections();

        return $wikitextSections->first(function (Section $wikiSection) use ($section) {
            return ($wikiSection->getTitle() == $section->getTitle())
                && ($wikiSection->getLevel() == $section->getLevel());
        });
    }

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

    protected function normalizeImages(array $images)
    {
        return collect($images)->map(function ($image) {
            return $this->normalizeImage($image);
        })->toArray();
    }

    protected function normalizeImage(array $image)
    {
        $image['title'] = (new LocaleFile)->normalize($image['title']);
        $image['title'] = (new Underscores)->normalize($image['title']);

        return $image;
    }

    protected function normalizeWikitext($wikitext)
    {
        $wikitext = (new LocaleFile)->normalize($wikitext);
        $wikitext = (new Underscores)->normalize($wikitext);

        return $wikitext;
    }

    protected function normalizeSection(Section $section)
    {
        $body = $section->getBody();

        $body = (new MultilineFile)->flatten($body);
        $body = (new MultilineTemplate)->flatten($body);

        $section->setBody($body);
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
