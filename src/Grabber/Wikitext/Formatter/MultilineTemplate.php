<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Formatter;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineTemplate
{
    public function flatten(Section $section)
    {
        $flatten = collect();

        $isTemplateOpened = false;
        $lines = preg_split("/\r\n|\n|\r/", $section->getBody());
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

            $line = str_replace("{{{$template}", '/!! IWG_TEMPLATE !!/', $line);

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
