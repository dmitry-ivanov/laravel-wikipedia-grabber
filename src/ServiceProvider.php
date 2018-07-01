<?php

namespace Illuminated\Wikipedia;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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
