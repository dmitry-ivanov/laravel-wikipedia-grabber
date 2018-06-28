<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Normalizer;

use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineTemplate
{
    public function flatten($wikitext)
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

    protected function isTemplateOpened($line)
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
            if (!str_contains($line, "{{{$template}")) {
                continue;
            }

            $line = str_replace("{{{$template}", '/!! IWG_MULTILINE_TEMPLATE !!/', $line);

            return !$this->isTemplateClosed($line);
        }

        return false;
    }

    protected function isTemplateClosed($line)
    {
        $line = (new Wikitext($line))->plain();
        return str_contains($line, '}}');
    }
}
