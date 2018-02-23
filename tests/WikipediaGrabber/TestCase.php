<?php

namespace Illuminated\Wikipedia\WikipediaGrabber\Tests;

use Illuminated\Wikipedia\ServiceProvider;
use Mockery;

Mockery::globalHelpers();

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        $orchestraConfig = $this->getBasePath() . '/config/wikipedia-grabber.php';
        copy(__DIR__ . '/fixture/config/wikipedia-grabber.php', $orchestraConfig);

        parent::resolveApplicationConfiguration($app);

        unlink($orchestraConfig);
    }
}
