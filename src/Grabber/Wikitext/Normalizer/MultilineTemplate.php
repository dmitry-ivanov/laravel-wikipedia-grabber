<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

use Illuminate\Support\Str;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineTemplate
{
    /**
     * Flatten the multiline templates.
     */
    public function flatten(string $wikitext): string
    {
        $flatten = collect();

        $isTemplateOpened = false;
        $lines = preg_split("/\r\n|\n|\r/", $wikitext);
        foreach ($lines as $line) {
            if ($isTemplateOpened) {
                $flatten->push($flatten->pop() . ' ' . $line);
                $isTemplateOpened = !$this->isTemplateClosed($line);
            } else {
                $flatten->push($line);
                $isTemplateOpened = $this->isTemplateOpened($line);
            }
        }

        return $flatten->implode("\n");
    }

    /**
     * Determine whether the given line opens a multiline template or not.
     */
    protected function isTemplateOpened(string $line): bool
    {
        $line = mb_strtolower($line, 'utf-8');

        $templates = [
            'annotated image', 'описанное изображение',
            'css image crop', 'часть изображения',
            'multiple image', 'кратное изображение',
            'double image', 'сдвоенное изображение',
            'фоторяд', 'фотоколонка',
            'wide image', 'панорама',
            'photomontage', 'фотомонтаж',
            'image frame', 'рамка в стиле миниатюры',
            'listen', 'spoken', 'sample', 'музыкальный отрывок стиля', 'семпл', 'музос',
        ];
        foreach ($templates as $template) {
            if (!Str::contains($line, "{{{$template}")) {
                continue;
            }

            $line = str_replace("{{{$template}", '/!! IWG-MULTILINE-TEMPLATE !!/', $line);

            return !$this->isTemplateClosed($line);
        }

        return false;
    }

    /**
     * Determine whether the given line closes a multiline template or not.
     */
    protected function isTemplateClosed(string $line): bool
    {
        $line = (new Wikitext($line))->plain();

        return Str::contains($line, '}}');
    }
}
