<?php

namespace Illuminated\Wikipedia;

use GuzzleHttp\Client;
use Illuminated\Wikipedia\Grabber\PageGrabbing;
use Illuminated\Wikipedia\Grabber\PreviewGrabbing;

abstract class Grabber
{
    use PageGrabbing;
    use PreviewGrabbing;

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            // 'headers' => [
            //     'User-Agent' => 'Aforizmu.net (http://aforizmu.net; dmitry.g.ivanov@gmail.com); Powered by illuminated/wikipedia-grabber',
            //     'Api-User-Agent' => 'Aforizmu.net (http://aforizmu.net; dmitry.g.ivanov@gmail.com); Powered by illuminated/wikipedia-grabber',
            // ],
        ]);
    }

    abstract protected function getBaseUri();
}
