<?php

namespace Illuminated\Wikipedia\Grabber\Wikitext\Templates;

use Illuminated\Wikipedia\Grabber\Component\Section;
use Illuminated\Wikipedia\Grabber\Wikitext\Wikitext;

class MultilineTemplate
{
    protected $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function flatten()
    {
        $flatten = collect();

        $isTemplateOpened = false;
        $lines = preg_split("/\r\n|\n|\r/", $this->section->getBody());
        foreach ($lines as $line) {
            if ($isTemplateOpened) {
                $flatten->push($flatten->pop() . $line);
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

        $templates = ['listen'];
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
