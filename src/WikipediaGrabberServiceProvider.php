<?php

namespace Illuminated\Wikipedia;

use Illuminate\Support\ServiceProvider;

class WikipediaGrabberServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'wikipedia-grabber');
    }

    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('wikipedia-grabber.php'),
        ], 'config');
    }

    protected function getConfigPath()
    {
        return __DIR__ . '/../config/wikipedia-grabber.php';
    }
}
