<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsPreview
{
    protected $sections;
    protected $imagesResponseData;

    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        $this->sections = $sections;
        $this->imagesResponseData = $imagesResponseData;
    }

    public function pipe()
    {
        if (!$this->isPreview()) {
            return $this->sections;
        }

        $this->sections->first()->setTitle('');

        return $this->sections;
    }

    protected function isPreview()
    {
        return !empty($this->imagesResponseData['wikitext'])
            && ($this->imagesResponseData['wikitext'] == '/// IWG-PREVIEW ///')
            && ($this->sections->count() == 1);
    }
}
