<?php

namespace Illuminated\Wikipedia\Grabber\Parser;

use Illuminate\Support\Collection;
use Illuminated\Wikipedia\Grabber\Component\Image;
use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Component\SectionGalleryValidator;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\LocaleFile;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineFile;
use Illuminated\Wikipedia\Grabber\Wikitext\Normalizer\MultilineTemplate;
use Illuminated\Wikipedia\Grabber\Wikitext\Templates\DoubleImageTemplate;
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

        $this->wikitext = $imagesResponseData['wikitext'];
        $this->mainImage = $imagesResponseData['main_image'];
        $this->images = $this->skipMainImage(
            $this->getUsedImages($this->wikitext, $imagesResponseData['images'])
        );
    }

    public function filter()
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

    protected function skipMainImage(array $images)
    {
        return collect($images)->filter(function (array $image) {
            return !$this->isMainImage($image);
        })->toArray();
    }

    protected function isMainImage(array $image)
    {
        return (array_get($image, 'imageinfo.0.url') == $this->mainImage['original']['source']);
    }

    protected function getUsedImages($wikitext, array $images)
    {
        return collect($images)->filter(function (array $image) use ($wikitext) {
            $file = last(explode(':', $image['title']));
            return $this->isFileUsed($wikitext, $file);
        })->toArray();
    }

    protected function isFileUsed($wikitext, $file)
    {
        $fileWithSpaces = str_replace('_', ' ', $file);
        $fileWithUnderscores = str_replace(' ', '_', $file);

        return str_contains($wikitext, $file)
            || str_contains($wikitext, $fileWithSpaces)
            || str_contains($wikitext, $fileWithUnderscores);
    }

    protected function filterByExtensions(Section $wikitextSection, array $images)
    {
        if (!$wikitextSection->isMain()) {
            return $images;
        }

        return collect($images)->filter(function (array $image) {
            return ends_with(
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
            $pure = (new SectionGalleryValidator)->validate($objects['gallery']);
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

        $title = str_replace('Файл:', 'File:', $title); ////////////////////////////////////////////////////////////////

        $line = $this->getImageWikitextLine($wikitextSection->getBody(), $title, $file);

        $openTag = "[[{$title}";
        if (!str_contains($line, $openTag)) {
            if ($this->isDoubleImageTemplate($line)) {
                return (new DoubleImageTemplate($line))->extract($file);
            }

            if ($this->isMultipleImageTemplate($line)) {
                return (new MultipleImageTemplate($line))->extract($file);
            }

            if ($this->isAudioTemplate($line, $image, $matches)) {
                return head($matches);
            }

            return $line;
        }

        $placeholder = '/!! IWG_TITLE !!/';
        $line = str_replace_first($openTag, $placeholder, $line);
        $line = (new Wikitext($line))->removeLinks();
        $line = str_replace_first($placeholder, $openTag, $line);

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
        return !(starts_with($imageWikitext, '[[') && ends_with($imageWikitext, ']]'));
    }

    protected function forceGalleryDisplaying($imageWikitext)
    {
        $imageWikitext = str_replace_first('[[', '', $imageWikitext);
        $imageWikitext = str_replace_last(']]', '', $imageWikitext);

        return $imageWikitext;
    }

    protected function isDoubleImageTemplate($line)
    {
        $line = mb_strtolower($line, 'utf-8');

        return starts_with($line, ['{{double image', '{{сдвоенное изображение'])
            && ends_with($line, '}}');
    }

    protected function isMultipleImageTemplate($line)
    {
        $line = mb_strtolower($line, 'utf-8');

        return starts_with($line, ['{{multiple image', '{{кратное изображение', '{{фотоколонка', '{{listen'])
            && ends_with($line, '}}');
    }

    protected function isAudioTemplate($line, array $image, &$matches)
    {
        $file = last(explode(':', $image['title']));
        $fileWithSpaces = str_replace('_', ' ', $file);
        $fileWithUnderscores = str_replace(' ', '_', $file);

        $file = preg_quote($file, '/');
        $fileWithSpaces = preg_quote($fileWithSpaces, '/');
        $fileWithUnderscores = preg_quote($fileWithUnderscores, '/');

        return preg_match("/\{\{(audio|pronunciation).*?\|{$file}\|.*?\}\}/i", $line, $matches)
            || preg_match("/\{\{(audio|pronunciation).*?\|{$fileWithSpaces}\|.*?\}\}/i", $line, $matches)
            || preg_match("/\{\{(audio|pronunciation).*?\|{$fileWithUnderscores}\|.*?\}\}/i", $line, $matches);
    }

    protected function isGrayTable($line)
    {
        return preg_match('/^(\s*\|\s*)width(\s*)=/i', $line) || preg_match('/^(\s*\|\s*)align(\s*)=/i', $line);
    }

    protected function freeUsedImages(array $usedImages)
    {
        $usedImages = array_pluck($usedImages, 'title');
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
                $this->normalize($section);
            });
        }

        return $this->wikitextSections;
    }

    protected function normalize(Section $section)
    {
        $body = $section->getBody();

        $body = (new LocaleFile)->normalize($body);
        $body = (new MultilineFile)->flatten($body);
        $body = (new MultilineTemplate)->flatten($body);

        $section->setBody($body);
    }

    protected function getMainSection()
    {
        return $this->sections->first->isMain();
    }
}
