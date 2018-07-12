<?php

namespace Illuminated\Wikipedia\Grabber\Parser\Pipe;

use Illuminate\Support\Collection;

class SectionsInPreview
{
    protected $sections;
    protected $response;

    public function __construct(Collection $sections, array $imagesResponseData = null)
    {
        dd('should be refactored!!');
        $this->sections = $sections;
        $this->response = $imagesResponseData;
    }

    public function pipe()
    {
        if ($this->isPreview()) {
            $this->sections->first()->setTitle('');
        }

        return $this->sections;
    }

    protected function isPreview()
    {
        return !empty($this->response['wikitext'])
            && ($this->response['wikitext'] == '/// IWG-PREVIEW ///')
            && ($this->sections->count() == 1);
    }
}
