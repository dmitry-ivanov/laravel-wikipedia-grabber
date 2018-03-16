<?php

namespace Illuminated\Wikipedia\Grabber\Formatter;

class PlainFormatter extends Formatter
{
    public function section(array $section)
    {
        $titleTag = $this->sectionTitleTag($section);
        $title = $section['title'];
        $body = $section['body'];

        return "
            <{$titleTag}>{$title}</{$titleTag}>
            <pre>{$body}</pre>
        ";
    }
}
